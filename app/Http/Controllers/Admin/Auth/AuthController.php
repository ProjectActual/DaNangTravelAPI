<?php

namespace App\Http\Controllers\Admin\Auth;

use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Events\Dispatcher;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\Bridge\Scope;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\CryptKey;

use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;

class AuthController extends Controller
{
    // public function login(LoginRequest $request)
    // {
    //     $credentials = $request->all();

    //     if (!Auth::attempt($credentials))
    //     {
    //         return responses('Unauthorized', Response::HTTP_UNAUTHORIZED);
    //     }

    //     $user   = $request->user();
    //     $accessToken = $user->createToken('Laravel Password Grant Client');
    //     $token  = $accessToken->token;

    //     $token->save();

    //     $expires_at = Carbon::parse(
    //         $token->expires_at
    //     )->toDateTimeString();

    //     $data = [
    //         'access_token' => 'Bearer ' . $accessToken->accessToken,
    //         'token_type' => 'Bearer',
    //         'expires_at' => $expires_at,
    //     ];

    //     return responses('login successfully', Response::HTTP_OK, $data);
    // }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        if($request->user()) {
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {

        $user = Auth::guard('api')->user();

        $old_password = $user->password;

        if(!Hash::check($request->old_password, $old_password)) {
            return responses('password do not match', Response::HTTP_NOT_FOUND);
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

        return responses('Change password successfully', Response::HTTP_OK, $data);
    }
}
