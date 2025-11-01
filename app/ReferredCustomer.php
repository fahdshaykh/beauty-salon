<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferredCustomer extends Model
{
    protected $table = 'referred_customers';

    protected $fillable = [
        'company_id',
        'referred_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to the authenticated user's company.
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: auth()->user()->company_id ?? null;
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }
        return $query;
    }
}
