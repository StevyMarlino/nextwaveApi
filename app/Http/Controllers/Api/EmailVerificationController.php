<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{

    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(
                [
                    'status' => true,
                    'message' => __('Email Already Verified')
                ],200
            );
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(
            [
                'status' => true,
                'message' => __('verification link sent'),
            ],200
        );
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(
                [
                    'status' => true,
                    'message' => __('Email already verified'),
                ]
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'status' => true,
            'message' => __('Email has been verified')
        ]);
    }
}
