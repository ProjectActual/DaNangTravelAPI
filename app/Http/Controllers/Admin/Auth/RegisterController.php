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
     * register and send email authentication
     *
     * @param  RegisterRequest $request These are binding rules when requests are accepted
     * @return Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $credentials                     = $request->only(['first_name', 'last_name', 'email']);
        $credentials['password']         = bcrypt($request->password);
        $credentials['activation_token'] = Uuid::generate(4)->string;
        $user = $this->user->create($credentials);
        //Add role CONGTACVIEN for user registed
        $user->roles()->attach(Role::CODE_NAME['CONGTACVIEN']);
        //Send email authentication to the user
        $info = [
            'activation_token'  => $user->activation_token,
        ];
        SendMail::send($user->email, trans('notication.email.credential.register'), 'email.register', $info);
        return $this->responses(trans('notication.email.register'), Response::HTTP_OK);
    }

    /**
     * confirm activation_token when user click the token in email
     *
     * @param  string  $activation_token Token used for authentication
     * @return Illuminate\Http\Response
     */
    public function credential(Request $request, $activation_token)
    {
        $user = $this->user->findByField('activation_token', $activation_token)->first();
        //check token is exists
        if(empty($user)) {
            return $this->responseException(
                trans('notication.email.credential.expired'),
                Response::HTTP_NOT_FOUND,
                self::TYPE['EXPIRED']
            );
        }
        //If the token is over 10 days, the token will be disabled
        if (Carbon::parse($user->updated_at)->addMinutes(10080)->isPast()) {
            $this->user->delete($user->id);
            return $this->responseException(
                trans('notication.email.credential.expired'),
                Response::HTTP_NOT_FOUND,
                self::TYPE['EXPIRED']
            );
        }
        //Update credential user
        $credentials = [
            'active'           => User::ACTIVE[2],
            'activation_token' => '',
        ];
        $this->user->update($credentials, $user->id);
        return $this->responses(trans('notication.email.success'), Response::HTTP_OK);
    }
}
