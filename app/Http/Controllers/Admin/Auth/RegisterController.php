<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController;

use Uuid;
use Carbon\Carbon;
use App\Entities\User;
use App\Entities\Role;
use App\Services\SendMail;
use App\Http\Requests\Admin\RegisterRequest;
use App\Repositories\Eloquents\UserRepositoryEloquent;

class RegisterController extends BaseController
{
    /**
     * $user
     * @var repository
     */
    protected $user;

    public function __construct(UserRepositoryEloquent $user)
    {
        $this->user = $user;
        $this->user->skipPresenter();
    }

    /**
     * Đăng kí cộng tác viên và gửi mail xác nhận
     *
     * @pẩm  RegisterRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
     * @return object
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->user->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'activation_token'  => Uuid::generate(4)->string,
        ]);

        $user->roles()->attach(Role::CODE_NAME['CONGTACVIEN']);

        $info = [
            'activation_token'  => $user->activation_token,
        ];

        SendMail::send($user->email, trans('notication.email.credential.register'), 'email.register', $info);

        return $this->responses(trans('notication.email.register'), Response::HTTP_OK);
    }

    /**
     * chức năng xác nhận activation_token khi người dùng click vào token ở mail
     *
     * @param  string  $activation_token token dùng để \credential
     * @return object
     */
    public function credential(Request $request, $activation_token)
    {
        $user = $this->user->findByField('activation_token', $activation_token)->first();

        if(empty($user)) {
            return $this->responseException(
                trans('notication.email.credential.expired'),
                Response::HTTP_NOT_FOUND,
                self::TYPE['EXPIRED']
            );
        }

        if (Carbon::parse($user->updated_at)->addMinutes(4320)->isPast()) {
            $this->user->delete($user->id);
            return $this->responseException(
                trans('notication.email.credential.expired'),
                Response::HTTP_NOT_FOUND,
                self::TYPE['EXPIRED']
            );
        }

        $user->active           = User::ACTIVE[2];
        $user->activation_token = '';

        $user->save();

        return $this->responses(trans('notication.email.success'), Response::HTTP_OK);
    }
}
