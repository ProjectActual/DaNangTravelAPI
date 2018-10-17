<?php

namespace App\Http\Controllers\Admin\Auth;

use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Entities\User;
use App\Services\SendMail;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\ProfileRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Repositories\Eloquents\UserRepositoryEloquent;

class AuthController extends BaseController
{
    protected $userRepository;

    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->userRepository = $userRepository;
    }

/**
 * logout of the system
 *
 * @return Illuminate\Http\Response
 */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->responses('Successfully logged out', Response::HTTP_OK);
    }

/**
 * get information of user
 *
 * @return Illuminate\Http\Response
 */
    public function user(Request $request)
    {
        $profile = $this->userRepository->with(['roles'])
        ->withCount('posts')
        ->find($request->user()->id);
        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('profile'));
    }

/**
 * update information of user
 *
 * @param  ProfileRequest $request These are binding rules when requests are accepted
 * @return Illuminate\Http\Response
 */
    public function update(ProfileRequest $request)
    {
        $this->user->skipPresenter();
        $credential = $request->except(['email', 'password', 'active', 'avatar']);
        // check if the $request->avatar is not empty update avatar, the reverse is do nothing
        if(!empty($request->avatar)) {
            $credential['avatar'] = $request->avatar;
        }
        $this->userRepository->update($credential, $request->user()->id);
        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

/**
 * change password of user
 *
 * @param  ChangePasswordRequest $request These are binding rules when requests are accepted
 * @return Illuminate\Http\Response
 */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $old_password = $user->password;
        //compare $request->password and old password is not match
        if(!Hash::check($request->old_password, $old_password)) {
            return $this->responseErrors('password', trans('validation_custom.password.current'));
        }
        $user->password = bcrypt($request->new_password);
        //delete old token
        $user->token()->revoke();
        //generate new token
        $token = $user->createToken('newToken');
        $accessToken = $token->accessToken;
        $expires_at  = Carbon::parse($token->token->expires_at)->toDateTimeString();
        $user->save();
        $data = [
            "token_type"   => "Bearer",
            'access_token' => $accessToken,
            'expires_at'   => $expires_at
        ];
        return $this->responses(trans('notication.edit.change'), Response::HTTP_OK, $data);
    }
}
