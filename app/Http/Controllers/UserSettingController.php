<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update one or more user settings for the currently authenticated user.
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);
        if (!is_array($settings)) {
            $key = $request->input('key');
            $value = $request->input('value');
            if ($key !== null) {
                $settings = [$key => $value];
            }
        }

        // Normalize boolean values
        foreach ($settings as $k => $v) {
            if ($v === 'true' || $v === '1' || $v === 1 || $v === true) {
                $settings[$k] = true;
            } elseif ($v === 'false' || $v === '0' || $v === 0 || $v === false) {
                $settings[$k] = false;
            }
        }

        if (!empty($settings) && Auth::check()) {
            Auth::user()->setSettings($settings);
            return response()->json([
                'success'  => true,
                'message'  => 'Setting updated successfully.',
                'settings' => $settings,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No settings provided.',
        ], 400);
    }
}
