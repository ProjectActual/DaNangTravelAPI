<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

use Uuid;
use Carbon\Carbon;
use Mail;
use App\Mail\MailAdapter;
use App\Services\SendMail;
use App\Repositories\Contracts\UserRepository;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\ResetPasswordUserRequest;
use App\Repositories\Contracts\PasswordResetRepository;

class PasswordResetController extends BaseController
{
    protected $user;
    protected $passwordReset;

    public function __construct(
        UserRepository $user,
        PasswordResetRepository $passwordReset
    ){
        $this->user          = $user;
        $this->passwordReset = $passwordReset;
    }

/**
 * tạo yêu cầu khi người dùng quên mật khẩu
 * @param  ResetPasswordRequest $request [email]
 * @return json                         [tin nhắn và gửi mail]
 */
    public function create(ResetPasswordRequest $request)
    {
        $user = $this->user->findByEmail($request->email);

        //check email exists
        if(empty($user)) {
            return $this->responseErrors('email', trans('passwords.user'));
        }

        //generate token
        $token = Uuid::generate(4)->string;

        //kiểm tra đã từng tạo token cho email này chưa, nếu chưa thì tạo, nếu có thì update token
        $checkEmailReset = $this->passwordReset->findByEmail($request->email);
        if(empty($checkEmailReset)) {
            $passwordReset = $this->passwordReset->create([
                'email'     => $request->email,
                'token'     => $token,
            ]);
        } else {
            $passwordReset = $this->passwordReset->update([
                'email'     => $request->email,
                'token'     => $token,
            ], $checkEmailReset->id);
        }

        //gán token vào mail
        $info = [
            'token' => $passwordReset->token,
        ];
        if(!empty($passwordReset) && !empty($user)) {
            SendMail::send($request->email, 'Đặt lại mật khẩu mới', 'email.password_reset', $info);
        }

        return $this->responses(trans('passwords.sent'), Response::HTTP_OK);
    }

/**
 * xác nhận email  thông qua token được đính trong email
 * @param  string $token [mã xác thực email]
 * @return json        [data, tình trạng]
 */
    public function authenticateToken($token)
    {
        $passwordReset = $this->passwordReset->findByToken($token);

        if(empty($passwordReset)) {
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }

        return response()->json($passwordReset, 200);
    }

/**
 * thiết lập lại mật khẩu cho người dùng
 * @param  ResetPasswordUserRequest $request [instance passwordReset]
 * @return json                            [message]
 */
    public function reset(ResetPasswordUserRequest $request)
    {
        $passwordReset = $this->passwordReset
            ->findByEmailToken($request->tokenReset, $request->email);

        if(empty($passwordReset)) {
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }

        $user = $this->user->findByEmail($request->email);

        if(empty($user)) {
            return $this->responses(trans('passwords.user'), Response::HTTP_NOT_FOUND);
        }

        $user->password = bcrypt($request->password_reset);
        $user->save();

        $passwordReset->delete();

        SendMail::send($request->email, trans('passwords.reset'), 'email.password_reset_success');

        return $this->responses(trans('passwords.reset'), Response::HTTP_OK);
    }
}
