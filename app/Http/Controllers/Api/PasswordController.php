<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\ResetPasswordWithOtp;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

/**
 * Class PasswordController
 * @package App\Http\Controllers\Api
 */
class PasswordController extends Controller
{

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {

        if ($request->device === 'mobile') {

            $verify = User::where('email', $request->all()['email'])->exists();

            if ($verify) {
                $verify2 = DB::table('password_resets')->where([
                    ['email', $request->email]
                ]);

                if ($verify2->exists()) {
                    $verify2->delete();
                }

                $otp = random_int(100000, 999999);

                $password_reset = DB::table('password_resets')->insert([
                    'email' => $request->all()['email'],
                    'otp' => $otp,
                    'created_at' => Carbon::now()
                ]);

                if ($password_reset) {
                    Mail::to($request->email)->send(new ResetPasswordWithOtp($otp));

                    return new JsonResponse(
                        [
                            'success' => true,
                            'message' => "Please check your email for a 6 digit code"
                        ],
                        200
                    );
                }
            }
        } else {

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

    }


    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function reset(ResetPasswordRequest $request)
    {

        if ($request->device !== 'mobile') {
            $status = Password::reset(
                $request->only('email', 'password', 'token'),

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
                return response()->json([
                    'status' => true,
                    'message' => __($status)
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => __($status)
            ], 500);

        } else {

            $request->only('email', 'password');

            $user = User::where('email',$request->email);
            $user->update([
                'password'=>Hash::make($request->password),
                'remember_token' => Str::random(60),
            ]);
            
                event(new PasswordReset($user));

                return new JsonResponse(
                    [
                        'success' => true,
                        'message' => "Your password has been reset",
                    ],
                    200
                );

        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp' => ['required'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => $validator->errors()
                ],
                422);
        }

        $check = DB::table('password_resets')->where([
            ['email', $request->all()['email']],
            ['otp', $request->all()['otp']],
        ]);

        if ($check->exists()) {

            DB::table('password_resets')->where([
                ['email', $request->all()['email']],
                ['token', $request->all()['otp']],
            ])->delete();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "You can now reset your password"
                ],
                200
            );
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "Invalid otp"
                ],
                401
            );
        }
    }

}

