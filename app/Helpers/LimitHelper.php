<?php

namespace App\Helpers;

use App\Models\CompanyLimit;
use Illuminate\Support\Facades\Auth;

class LimitHelper
{
    public static function canCreate($resource)
    {
        $company = Auth::user()->company;
        $limits = $company->currentLimits() ?? self::initializeLimits($company);

        if ($limits->plan?->is_unlimited) {
            return true;
        }

        $field = $resource . '_used';
        $override = 'override_' . $resource;
        $max = $limits->$override ?? $limits->plan?->{'max_' . $resource};

        if (is_null($max)) return true; // no limit set

        return $limits->$field < $max;
    }

    public static function increment($resource)
    {
        $company = Auth::user()->company;
        $limits = $company->currentLimits() ?? self::initializeLimits($company);

        $field = $resource . '_used';
        $limits->increment($field);
    }

    private static function initializeLimits($company)
    {
        $currentMonth = now()->format('Y-m');

        // Reset if new month
        $existing = $company->limits()->where('current_month', $currentMonth)->first();
        if (!$existing) {
            $company->limits()->delete(); // clean old months
            return CompanyLimit::create([
                'company_id' => $company->id,
                'subscription_plan_id' => $company->subscription_plan_id ?? 1, // default plan id
                'current_month' => $currentMonth,
            ]);
        }

        return $existing;
    }

    public static function getUsage($resource)
    {
        $company = Auth::user()->company;
        $limits = $company->currentLimits();

        if (!$limits) return ['used' => 0, 'limit' => '∞'];

        $used = $limits->{$resource . '_used'};
        $override = $limits->{'override_' . $resource};
        $planLimit = $limits->plan?->{'max_' . $resource};

        $limit = $override ?? $planLimit ?? '∞';

        return compact('used', 'limit');
    }
}