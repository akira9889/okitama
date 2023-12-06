<?php

namespace App\Policies;

use App\Models\DropoffHistory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DropoffHistoryPolicy
{
    public function show(User $user, DropoffHistory $dropoffHistory)
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->id === $dropoffHistory->user_id;
    }
}
