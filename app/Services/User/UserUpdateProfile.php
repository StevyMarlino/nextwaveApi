<?php


namespace App\Services\User;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserUpdateProfile
{
    public function update(array $request, User $user)
    {
        if (is_null($request['password'])) {
            $user->update([
                'last_name' => $request['last_name'] ?? $user->last_name,
                'phone' => $request['phone'] ?? $user->phone,
            ]);
        } else {
            if (!Hash::check($request['current_password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'Credentials don\'t match'
                ], 403);
            }
            $user->update([
                'last_name' => $request['last_name'] ?? $user->last_name,
                'phone' => $request['phone'] ?? $user->phone,
                'password' => Hash::make($request['password']),

            ]);
        }

        return $user;
    }

}
