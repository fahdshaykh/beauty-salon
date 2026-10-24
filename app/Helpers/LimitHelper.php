<?php

namespace App\Helpers;

use App\Models\CompanyLimit;
use Illuminate\Support\Facades\Auth;

class LimitHelper
{
    public static function canCreate($resource)
    {
        $company = Auth::user()?->company;
        if (!$company) return true;
        $limits = $company->currentLimits();

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
        $company = Auth::user()?->company;
        if (!$company) return;
        $limits = $company->currentLimits();

        $field = $resource . '_used';
        $limits->increment($field);
    }

    private static function initializeLimits($company)
    {
        return $company->currentLimits();
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