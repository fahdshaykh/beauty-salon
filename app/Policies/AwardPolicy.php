<?php

namespace App\Policies;

use App\User;
use App\Models\Award;

class AwardPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view awards');
    }

    public function view(User $user, Award $award)
    {
        return $user->can('view awards') && $user->company_id === $award->company_id;
    }

    public function create(User $user)
    {
        return $user->can('create awards');
    }

    public function update(User $user, Award $award)
    {
        return $user->can('edit awards') && $user->company_id === $award->company_id;
    }

    public function delete(User $user, Award $award)
    {
        return $user->can('delete awards') && $user->company_id === $award->company_id;
    }
}
