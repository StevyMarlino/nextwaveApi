<?php


namespace App\Services\User;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCreate
{

    public function create(array $data) : User
    {
        return User::create([
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'phone' => $data['phone'],
            'poste' => $data['poste'],
            'is_active' => true,
            'image' => $data['image'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
