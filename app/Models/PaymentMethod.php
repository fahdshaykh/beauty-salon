<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'method',
        'description',
        'fields',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'fields'    => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Tenant scope for multi-company isolation
     */
    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });
    }
}
