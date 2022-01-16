<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class PasswordController
 * @package App\Http\Controllers\Api
 */
class PasswordController extends Controller
{

    /**
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(
                [
                    'status' => true,
                    'message' => __($status),
                ]
            );
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }


    /**
     * @param ResetPasswordRequest $request
     * @return Application|ResponseFactory|Response
     * @unauthenticated
     */
    public function reset(ResetPasswordRequest $request)
    {

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'status' => true,
                'message' => __($status)
            ]);
        }

        return response([
            'status' => false,
            'message' => __($status)
        ], 500);

    }


}
