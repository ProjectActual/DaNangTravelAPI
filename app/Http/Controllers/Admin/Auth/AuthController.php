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
    protected $user;

    public function __construct(UserRepositoryEloquent $user)
    {
        $this->user = $user;
    }

/**
 * chức năng đăng xuất khỏi hệ thống
 *
 * @return object
 */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->responses('Successfully logged out', Response::HTTP_OK);
    }

/**
 * lấy thông tin của người dùng
 *
 * @return object
 */
    public function user(Request $request)
    {
        $profile = $this->user->with(['roles'])
        ->withCount('posts')
        ->find($request->user()->id);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('profile'));
    }

/**
 * Cập nhật thông tin của người.
 *
 * @param  ProfileRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
 *
 * @return object
 */
    public function update(ProfileRequest $request)
    {
        $this->user->skipPresenter();

        $user = $this->user->find($request->user()->id);
        if(!empty($request->avatar)) {
            $user->avatar     = $request->avatar;
        }

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->phone      = $request->phone;
        $user->gender     = $request->gender;
        $user->birthday   = $request->birthday;

        $user->save();

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

/**
 * thay đôi mật khẩu của người dùng
 *
 * @param  ChangePasswordRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
 * @return object
 */
    public function changePassword(ChangePasswordRequest $request)
    {

        $user = $request->user();

        $old_password = $user->password;

        if(!Hash::check($request->old_password, $old_password)) {
            return $this->responseErrors('password', trans('validation_custom.password.current'));
        }

        $user->password = bcrypt($request->new_password);
        $user->token()->revoke();
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
