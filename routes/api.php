<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    //Authentification Route
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

    Route::get('/user/auth', [\App\Http\Controllers\UserController::class, 'show']);
    Route::post('/user-update-info', [\App\Http\Controllers\UserController::class, 'update']);
    Route::put('change-avatar', [\App\Http\Controllers\UserController::class, 'ChangeAvatar']);

});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [PasswordController::class, 'forgotPassword']);
Route::post('verify-otp', [PasswordController::class, 'verifyOtp']);
Route::post('reset-password', [PasswordController::class, 'reset']);
