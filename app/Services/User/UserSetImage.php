<?php


namespace App\Services\User;


use App\Models\User;
use Nette\Utils\Image;

class UserSetImage
{

    public function SetAvatar($request, User $user)
    {
        $photo = Image::make($request->file('image'));
        $photo->resize(800, 800)->save($user->name . 'jpg');
        $user->update([
            'image' => $photo,
        ]);
        return $user;
    }
}
