<?php

namespace App\Http\Controllers;

use App\Http\Requests\checkExistingMailRequest;
use App\Http\Requests\User\UserUpdateAvatarRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserSetImage;
use App\Services\User\UserUpdateProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => new \App\Http\Resources\UserResource($request->user())
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request)
    {
        try {
            $user = (new UserUpdateProfile)->update($request->all(), Auth::user());

            return response()->json(
                [
                    'status' => true,
                    'message' => __('User Information Updated'),
                    'user' => new UserResource($user),
                ]
            );

        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

    }

    public function ChangeAvatar(UserUpdateAvatarRequest $request)
    {
        try {
            $user = (new UserSetImage)->SetAvatar($request, Auth::user());
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Your Profile picture was updated successfully',
                    'user' => new UserResource($user)
                ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    public function checkExistEmail(checkExistingMailRequest $request)
    {
        $exist = User::whereEmail($request->email)->exists();
        
        return response()->json([
            'status' => $exist,
            'message' => $exist ? 'Email is already exist' : 'Email is not taken'
        ]);
    }
}
