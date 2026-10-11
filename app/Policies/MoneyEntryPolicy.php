<?php

namespace App\Policies;

use App\Models\MoneyEntry;
use App\Models\User;

class MoneyEntryPolicy
{
    public function update(User $user, MoneyEntry $moneyEntry): bool
    {
        return $user->id === $moneyEntry->user_id;
    }

    public function delete(User $user, MoneyEntry $moneyEntry): bool
    {
        return $this->update($user, $moneyEntry);
    }
}
