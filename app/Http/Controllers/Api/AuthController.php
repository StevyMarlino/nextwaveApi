<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\User\UserCreate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {

        $user = (new UserCreate)->create($request->all());

        event(new Registered($user));

        $token = $user->createToken('authtokensnextwave');

        return response()->json(
            [
                'status' => true,
                'message' => __('User Registered, Please Check our Mail'),
                'user' => new UserResource($user),
                'token' => $token->plainTextToken
            ]
        );

    }


    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $token = $request->user()->createToken('authtokensnextwave');

        return response()->json(
            [
                'status' => true,
                'message' => __('You are Logged'),
                'user' => new UserResource(Auth::user()),
                'token' => $token->plainTextToken
            ]
        );
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {

        auth()->user()->tokens()->delete();

        return response()->json(
            [
                'status' => true,
                'message' => __('You Logged out')
            ]
        );

    }

}
