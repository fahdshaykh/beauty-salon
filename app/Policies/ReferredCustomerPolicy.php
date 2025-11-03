<?php

namespace App\Policies;

use App\User;
use App\ReferredCustomer;

class ReferredCustomerPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view referred customers');
    }

    public function view(User $user, ReferredCustomer $referredCustomer)
    {
        return $user->can('view referred customers') && $user->company_id === $referredCustomer->company_id;
    }

    public function create(User $user)
    {
        return $user->can('create referred customers');
    }

    public function update(User $user, ReferredCustomer $referredCustomer)
    {
        return $user->can('edit referred customers') && $user->company_id === $referredCustomer->company_id;
    }

    public function delete(User $user, ReferredCustomer $referredCustomer)
    {
        return $user->can('delete referred customers') && $user->company_id === $referredCustomer->company_id;
    }
}
