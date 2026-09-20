<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function update(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->user_id === $service->user_id;
    }

    public function delete(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->user_id === $service->user_id;
    }

    public function uploadDocument(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->user_id === $service->user_id;
    }
}
