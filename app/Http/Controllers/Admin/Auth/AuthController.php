<?php

namespace App\Http\Controllers\Admin\Auth;

use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        return responses('Successfully logged out', Response::HTTP_OK);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {

        $user = $request->user();

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

        return responses(trans('notication.edit.change'), Response::HTTP_OK, $data);
    }
}
