<?php

namespace App\Http\Controllers\Admin\Auth;

use DB;
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
    protected $userRepository;
    protected $passwordResetRepository;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository
    ){
        $this->userRepository          = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

/**
 * Create a request when user forget the password
 *
 * @param  ResetPasswordRequest $request These are binding rules when requests are accepted
 * @return Illuminate\Http\Response
 */
    public function create(ResetPasswordRequest $request)
    {
        $user = $this->userRepository->findByEmail($request->email);
        $credentials = $request->only('email');
        //check email exists
        if(empty($user)) {
            return $this->responseErrors('email', trans('passwords.user'));
        }
        //generate token
        $credentials['token'] = Uuid::generate(4)->string;
        //The token check has been initialized, If not then start creating a token, contrary update the token
        $checkEmailReset = $this->passwordResetRepository->findByEmail($request->email);
        if(empty($checkEmailReset)) {
            $passwordReset = $this->passwordResetRepository->create($credentials);
        } else {
            $passwordReset = $this->passwordResetRepository->update($credentials, $checkEmailReset->id);
        }
        //Assign token into email
        $info = [
            'token' => $passwordReset->token,
        ];
        //send email reset password
        SendMail::send($request->email, 'Đặt lại mật khẩu mới', 'email.password_reset', $info);
        return $this->responses(trans('passwords.sent'), Response::HTTP_OK);
    }

/**
 * confirm email via the attached token in the email
 *
 * @param  string $token
 * @return Illuminate\Http\Response
 */
    public function authenticateToken($token)
    {
        $passwordReset = $this->passwordResetRepository->findByToken($token);
        //check token is exists
        if(empty($passwordReset)) {
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }
        //If the token is over 12 hours, the token will be disabled
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $this->passwordResetRepository->delete($passwordReset->id);
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }
        return response()->json($passwordReset, 200);
    }

/**
 * setting password for user
 *
 * @param  ResetPasswordUserRequest $request These are binding rules when requests are accepted
 * @return Illuminate\Http\Response
 */
    public function reset(ResetPasswordUserRequest $request)
    {
        //check email and token exists in table password_resets
        $passwordReset = $this->passwordResetRepository
            ->findByEmailToken($request->tokenReset, $request->email);
        //if empty is disable
        if(empty($passwordReset)) {
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }
        $user = $this->userRepository->findByEmail($request->email);
        //check if user empty is disable
        if(empty($user)) {
            return $this->responses(trans('passwords.user'), Response::HTTP_NOT_FOUND);
        }
        DB::beginTransaction();
        try {
            $credential = [
                'password' => bcrypt($request->password_reset),
            ];
            $this->userRepository->update($credential, $user->id);
            $this->passwordResetRepository->delete($passwordReset->id);
            //send mail password reset success
            SendMail::send($request->email, trans('passwords.reset'), 'email.password_reset_success');
            DB::commit();
            return $this->responses(trans('passwords.reset'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
        }
    }
}
