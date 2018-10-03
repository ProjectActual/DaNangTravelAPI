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
        $this->user = $user;
        $this->passwordReset = $passwordReset;
    }

    public function create(ResetPasswordRequest $request)
    {
        $user = $this->user->findByEmail($request->email);

        if(empty($user)) {
            return $this->responseErrors('email', trans('passwords.user'));
        }

        $token = Uuid::generate(4)->string;

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

        $info = [
            'token' => $passwordReset->token,
        ];

        if(!empty($passwordReset) && !empty($user)) {
            SendMail::send($request->email, 'Đặt lại mật khẩu mới', 'email.password_reset', $info);
        }

        return $this->responses(trans('passwords.sent'), Response::HTTP_OK);
    }

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
