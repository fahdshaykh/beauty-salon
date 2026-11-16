<?php

namespace App\Traits;

use App\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Boot the BelongsToCompany trait.
     * Automatically scopes queries and sets company_id on model creation.
     */
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check() && Auth::user()->company_id) {
                $builder->where($builder->getModel()->getTable() . '.company_id', Auth::user()->company_id);
            }
        });

        static::creating(function (Model $model) {
            if (Auth::check() && Auth::user()->company_id && empty($model->company_id)) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    /**
     * Relationship to the owning tenant Company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

