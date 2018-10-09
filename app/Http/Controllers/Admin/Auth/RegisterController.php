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
    protected $user;

    public function __construct(UserRepositoryEloquent $user)
    {
        $this->user = $user;
        $this->user->skipPresenter();
    }

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

        SendMail::send($user->email, trans('notication.email.credential'), 'email.register', $info);

        return $this->responses(trans('notication.email.register'), Response::HTTP_OK);
    }

    public function credential(Request $request, $activation_token)
    {
        $user = $this->user->findByField('activation_token', $activation_token)->first();

        if(empty($user)) {
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }

        if (Carbon::parse($user->updated_at)->addMinutes(720)->isPast()) {
            return $this->responses(trans('passwords.token'), Response::HTTP_NOT_FOUND);
        }

        $user->active           = User::ACTIVE[1];
        $user->activation_token = '';

        $user->save();

        return $this->responses(trans('notication.email.success'), Response::HTTP_OK);
    }
}
