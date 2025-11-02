<?php

namespace App\Policies;

use App\User;
use App\ExpenseType;
use Illuminate\Support\Facades\Log;

class ExpenseTypePolicy
{
    /**
     * Determine whether the user can view any expense types.
     */
    public function viewAny(User $user)
    {
        return $user->can('view expense types');
    }

    /**
     * Determine whether the user can view the expense type.
     */
    public function view(User $user, ExpenseType $expenseType)
    {
        return $user->can('view expense types') && $user->company_id === $expenseType->company_id;
    }

    /**
     * Determine whether the user can create expense types.
     */
    public function create(User $user)
    {
        return $user->can('create expense types');
    }

    /**
     * Determine whether the user can update the expense type.
     */
    public function update(User $user, ExpenseType $expenseType)
    {
        $hasPermission = $user->can('edit expense types');
        $sameCompany = $user->company_id === $expenseType->company_id;
        
        // Log policy check details for debugging
        \Log::debug('ExpenseType Policy Update Check', [
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'expense_type_id' => $expenseType->id,
            'expense_type_company' => $expenseType->company_id,
            'has_permission' => $hasPermission,
            'same_company' => $sameCompany
        ]);

        return $hasPermission && $sameCompany;
    }

    /**
     * Determine whether the user can delete the expense type.
     */
    public function delete(User $user, ExpenseType $expenseType)
    {
        return $user->can('delete expense types') && $user->company_id === $expenseType->company_id;
    }
}
