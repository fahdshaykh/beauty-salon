<?php

namespace App\Policies;

use App\User;
use App\Customer;

class CustomerPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view customers');
    }

    public function view(User $user, Customer $customer)
    {
        return $user->can('view customers') && $user->company_id === $customer->company_id;
    }

    public function create(User $user)
    {
        return $user->can('create customers');
    }

    public function update(User $user, Customer $customer)
    {
        return $user->can('edit customers') && $user->company_id === $customer->company_id;
    }

    public function delete(User $user, Customer $customer)
    {
        return $user->can('delete customers') && $user->company_id === $customer->company_id;
    }
}
