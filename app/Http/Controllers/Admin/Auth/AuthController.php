<?php

namespace App\Http\Controllers\Admin\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Admin\LoginRequest;
use App\Repositories\Contracts\UserRepository;

class AuthController extends Controller
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->all();

        if (!Auth::attempt($credentials))
        {
            return responses('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $user   = $request->user();
        $accessToken = $user->createToken('token_admin');
        $token  = $accessToken->token;

        $token->save();

        $expires_at = Carbon::parse(
            $token->expires_at
        )->toDateTimeString();

        $data = [
            'access_token' => 'Bearer ' . $accessToken->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $expires_at,
        ];

        return responses('login successfully', Response::HTTP_OK, $data);
    }

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
}
