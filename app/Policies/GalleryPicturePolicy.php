<?php

namespace App\Policies;

use App\Models\GalleryPicture;
use App\Models\User;

class GalleryPicturePolicy
{
    public function update(User $user, GalleryPicture $galleryPicture): bool
    {
        return $user->id === $galleryPicture->user_id;
    }

    public function delete(User $user, GalleryPicture $galleryPicture): bool
    {
        return $this->update($user, $galleryPicture);
    }
}
