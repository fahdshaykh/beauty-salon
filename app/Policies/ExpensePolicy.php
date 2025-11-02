<?php

namespace App\Policies;

use App\User;
use App\Expense;

class ExpensePolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view expenses');
    }

    public function view(User $user, Expense $expense)
    {
        return $user->can('view expenses') && $user->company_id === $expense->company_id;
    }

    public function create(User $user)
    {
        return $user->can('create expenses');
    }

    public function update(User $user, Expense $expense)
    {
        return $user->can('edit expenses') && $user->company_id === $expense->company_id;
    }

    public function delete(User $user, Expense $expense)
    {
        return $user->can('delete expenses') && $user->company_id === $expense->company_id;
    }
}
