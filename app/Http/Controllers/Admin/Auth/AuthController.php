<?php

namespace App\Http\Controllers\Admin\Auth;

use DB;
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
        return $this->responses(trans('notication.logout'), Response::HTTP_OK);
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
        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, $profile);
    }

    /**
     * update information of user
     *
     * @param  ProfileRequest $request These are binding rules when requests are accepted
     * @return Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        $this->userRepository->skipPresenter();
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
        $user         = $request->user();
        $old_password = $user->password;
        //compare $request->password and old password is not match
        if(!Hash::check($request->old_password, $old_password)) {
            return $this->responseErrors('password', trans('validation_custom.password.current'));
        }
        DB::beginTransaction();
        try {
            $credential = [
                'password'  => bcrypt($request->new_password)
            ];
            //delete old token
            $user->token()->revoke();
            //generate new token
            $tokenResult       = $user->createToken('newToken');
            $token             = $tokenResult->token;
            //update expires_at is 4 week for token;
            $token->expires_at = Carbon::now()->addWeeks(4);
            $token->save();
            $expires_at        = Carbon::parse($token->expires_at)->toDateTimeString();
            $this->userRepository->update($credential, $user->id);
            $data = [
                'type'         => 'token',
                'token_type'   => 'Bearer',
                'access_token' => $tokenResult->accessToken,
                'expires_at'   => $expires_at
            ];
            DB::commit();
            return $this->responses(trans('notication.edit.change'), Response::HTTP_OK, $data);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
