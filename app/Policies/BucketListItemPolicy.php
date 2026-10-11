<?php

namespace App\Policies;

use App\Models\BucketListItem;
use App\Models\User;

class BucketListItemPolicy
{
    public function update(User $user, BucketListItem $bucketListItem): bool
    {
        return $user->id === $bucketListItem->user_id;
    }

    public function delete(User $user, BucketListItem $bucketListItem): bool
    {
        return $this->update($user, $bucketListItem);
    }
}
