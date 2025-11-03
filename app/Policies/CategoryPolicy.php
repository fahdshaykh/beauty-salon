<?php

namespace App\Policies;

use App\User;
use App\Category;

class CategoryPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view categories');
    }

    public function view(User $user, Category $category)
    {
        return $user->can('view categories') && $user->company_id === $category->company_id;
    }

    public function create(User $user)
    {
        return $user->can('create categories');
    }

    public function update(User $user, Category $category)
    {
        return $user->can('edit categories') && $user->company_id === $category->company_id;
    }

    public function delete(User $user, Category $category)
    {
        return $user->can('delete categories') && $user->company_id === $category->company_id;
    }
}
