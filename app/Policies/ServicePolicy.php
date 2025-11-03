<?php

namespace App\Policies;

use App\User;
use App\Services;

class ServicePolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view services');
    }

    public function view(User $user, Services $service)
    {
        return $user->can('view services') && $user->company_id === $service->company_id;
    }

    public function create(User $user)
    {
        return $user->can('create services');
    }

    public function update(User $user, Services $service)
    {
        return $user->can('edit services') && $user->company_id === $service->company_id;
    }

    public function delete(User $user, Services $service)
    {
        return $user->can('delete services') && $user->company_id === $service->company_id;
    }
}
