<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Oauth2;
use Throwable;

class GoogleCalendarOAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!(auth()->user() && auth()->user()->hasRole('admin'))) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Create and configure a Google_Client instance for OAuth.
     * Supports per-company client ID and secret from company_settings,
     * with graceful fallback to oauth-credentials.json or .env configuration.
     */
    public static function createOAuthClient(?int $companyId = null): ?Google_Client
    {
        $client = new Google_Client();

        if ($companyId === null) {
            $companyId = session('google_oauth_company_id') ?: (auth()->check() ? auth()->user()->company_id : null);
        }

        $setting = null;
        if ($companyId) {
            $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        }

        $companyClientId = !empty($setting->google_client_id) ? trim($setting->google_client_id) : null;
        $companyClientSecret = !empty($setting->google_client_secret) ? trim($setting->google_client_secret) : null;

        $credentialsJson = config('google-calendar.auth_profiles.oauth.credentials_json') 
            ?: storage_path('app/google-calendar/oauth-credentials.json');

        if (!empty($companyClientId) && !empty($companyClientSecret)) {
            // Tenant has their own client credentials stored in company_settings
            $client->setClientId($companyClientId);
            $client->setClientSecret($companyClientSecret);
        } elseif (file_exists($credentialsJson)) {
            // Fallback to local credentials JSON file
            $client->setAuthConfig($credentialsJson);
        } else {
            // Fallback to .env configuration
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');

            if (empty($clientId) || empty($clientSecret)) {
                return null;
            }

            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
        }

        $redirectUri = config('services.google.redirect_uri');

        if (empty($redirectUri) && file_exists($credentialsJson)) {
            $jsonContent = @file_get_contents($credentialsJson);
            $jsonData = json_decode($jsonContent, true);
            $configuredUris = $jsonData['web']['redirect_uris'] ?? ($jsonData['installed']['redirect_uris'] ?? []);

            if (!empty($configuredUris)) {
                $currentCallback = request() ? request()->root() . '/google-calendar/callback' : null;
                if ($currentCallback && in_array($currentCallback, $configuredUris, true)) {
                    $redirectUri = $currentCallback;
                } else {
                    $redirectUri = $configuredUris[0];
                }
            }
        }

        if (empty($redirectUri)) {
            $redirectUri = route('google.calendar.callback');
        }

        $client->setRedirectUri($redirectUri);
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');
        $client->setIncludeGrantedScopes(true);
        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
            Google_Service_Calendar::CALENDAR_EVENTS,
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'openid',
        ]);

        return $client;
    }

    /**
     * Redirect salon owner to Google's OAuth 2.0 consent screen.
     */
    public function connect(Request $request)
    {
        $companyId = auth()->user()->company_id;
        session(['google_oauth_company_id' => $companyId]);

        $client = self::createOAuthClient($companyId);

        if (!$client) {
            return redirect()->route('theme.settings')
                ->with('error', 'Google Client ID and Client Secret are not configured. Please enter and save them in the form below first.');
        }

        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handle Google OAuth 2.0 callback and store tokens.
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('theme.settings')
                ->with('error', 'Google authorization was cancelled or failed: ' . $request->get('error'));
        }

        $code = $request->get('code');
        if (empty($code)) {
            return redirect()->route('theme.settings')
                ->with('error', 'Authorization code not received from Google.');
        }

        $companyId = session('google_oauth_company_id') ?: auth()->user()->company_id;

        $client = self::createOAuthClient($companyId);
        if (!$client) {
            return redirect()->route('theme.settings')
                ->with('error', 'Google OAuth client configuration missing.');
        }

        try {
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                Log::error('Google OAuth token fetch error: ' . json_encode($token));
                return redirect()->route('theme.settings')
                    ->with('error', 'Failed to retrieve access token: ' . ($token['error_description'] ?? $token['error']));
            }

            // Verify that Google Calendar permission was actually granted by user
            $grantedScope = $token['scope'] ?? '';
            if (!str_contains($grantedScope, 'calendar')) {
                Log::warning('Google OAuth connected without calendar scope: ' . $grantedScope);
                return redirect()->route('theme.settings')
                    ->with('error', 'Google Calendar permission was NOT granted! When authorizing with Google, you must check the box "See, edit, share, and permanently delete all the calendars you can access using Google Calendar" on Google\'s permission screen. Also ensure the Calendar scope is added in your Google Cloud Console OAuth consent screen.');
            }

            $client->setAccessToken($token);

            // Fetch authenticated user profile email
            $accountEmail = null;
            try {
                $oauth2 = new Google_Service_Oauth2($client);
                $userInfo = $oauth2->userinfo->get();
                $accountEmail = $userInfo->email ?? null;
            } catch (Throwable $e) {
                Log::warning('Could not fetch Google userinfo email: ' . $e->getMessage());
            }

            $currentSetting = DB::table('company_settings')->where('company_id', $companyId)->first();
            $currentCalendarId = trim($currentSetting->google_calendar_id ?? '') ?: 'primary';

            DB::table('company_settings')->where('company_id', $companyId)->update([
                'google_access_token'  => json_encode($token),
                'google_account_email' => $accountEmail,
                'google_calendar_id'   => $accountEmail, //$currentCalendarId,
                'google_calendar_sync' => 1,
                'updated_at'           => now(),
            ]);

            // Attempt to retroactively sync recent unsynced bookings for this company
            try {
                $unsyncedBookings = DB::table('service_bookings')
                    ->where('company_id', $companyId)
                    ->whereNull('google_event_id')
                    ->whereNull('deleted_at')
                    ->where(function ($q) {
                        $q->whereNull('status')->orWhereNotIn('status', [4]);
                    })
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->pluck('id');

                $notificationService = app(\App\Services\Bookings\BookingNotificationService::class);
                foreach ($unsyncedBookings as $bId) {
                    $notificationService->syncGoogleCalendarEvent((int)$bId, $companyId);
                }
            } catch (Throwable $syncEx) {
                Log::warning('Post-connect booking sync error: ' . $syncEx->getMessage());
            }

            return redirect()->route('theme.settings')
                ->with('success', 'Google Calendar connected successfully' . ($accountEmail ? ' as ' . $accountEmail : '') . '!');
        } catch (Throwable $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('theme.settings')
                ->with('error', 'An error occurred during Google connection: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google Calendar for this company.
     */
    public function disconnect(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        if ($setting && !empty($setting->google_access_token)) {
            try {
                $client = self::createOAuthClient($companyId);
                if ($client) {
                    $token = json_decode($setting->google_access_token, true);
                    $client->revokeToken($token);
                }
            } catch (Throwable $e) {
                Log::warning('Could not revoke Google OAuth token: ' . $e->getMessage());
            }
        }

        DB::table('company_settings')->where('company_id', $companyId)->update([
            'google_access_token'  => null,
            'google_account_email' => null,
            'google_calendar_sync' => 0,
            'updated_at'           => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Google Calendar disconnected successfully.']);
        }

        return redirect()->route('theme.settings')
            ->with('success', 'Google Calendar disconnected successfully.');
    }
}
