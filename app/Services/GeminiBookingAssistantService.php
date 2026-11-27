<?php

namespace App\Services;

use App\Company;
use App\Category;
use App\Services as SalonService;
use App\User;
use App\CompanySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiBookingAssistantService
{
    /**
     * Process conversational booking query with Gemini API.
     */
    public function chat(int $companyId, string $userMessage, array $conversationHistory = []): array
    {
        $company = Company::find($companyId);
        if (!$company) {
            return [
                'success' => false,
                'message' => 'Company not found.',
            ];
        }

        $apiKey = $this->resolveApiKey($companyId);

        // Fetch company catalog context
        $categories = Category::where('company_id', $companyId)
            ->where('is_active', 1)
            ->with(['services_booking' => function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            }])
            ->get();

        $servicesList = [];
        foreach ($categories as $cat) {
            foreach ($cat->services_booking as $srv) {
                $servicesList[] = [
                    'id'          => $srv->id,
                    'name'        => $srv->name,
                    'category'    => $cat->name,
                    'price'       => (float) $srv->price,
                    'duration'    => (int) ($srv->duration ?: 30) . ' mins',
                    'description' => $srv->description ?? '',
                ];
            }
        }

        $technicians = User::where('company_id', $companyId)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get(['id', 'name', 'position'])
            ->toArray();

        $companySetting = DB::table('company_settings')->where('company_id', $companyId)->first();
        $assistantName = $companySetting->ai_assistant_name ?? 'Smart Booking Assistant';
        $customInstructions = $companySetting->ai_instructions ?? '';

        // If no Gemini API key configured, use intelligent keyword-based fallback
        if (empty($apiKey)) {
            return $this->smartFallback($userMessage, $company, $servicesList, $assistantName);
        }

        return $this->callGeminiApi(
            $apiKey,
            $userMessage,
            $conversationHistory,
            $company,
            $servicesList,
            $technicians,
            $assistantName,
            $customInstructions
        );
    }

    /**
     * Resolve effective Gemini API key (company custom key or global .env fallback).
     */
    protected function resolveApiKey(int $companyId): ?string
    {
        $companySetting = DB::table('company_settings')->where('company_id', $companyId)->first();
        if (!empty($companySetting->gemini_api_key)) {
            return trim($companySetting->gemini_api_key);
        }

        return env('GEMINI_API_KEY', config('services.gemini.key'));
    }

    /**
     * Call Google Gemini API (gemini-1.5-flash) with structured output.
     */
    protected function callGeminiApi(
        string $apiKey,
        string $userMessage,
        array $conversationHistory,
        Company $company,
        array $services,
        array $technicians,
        string $assistantName,
        string $customInstructions
    ): array {
        $todayStr = now()->format('Y-m-d l');
        $companyName = $company->company_name ?: 'Our Service Center';

        $systemPrompt = "You are {$assistantName}, an expert booking assistant for '{$companyName}'.
Current date: {$todayStr}.

Company Services Catalog:
" . json_encode($services, JSON_PRETTY_PRINT) . "

Available Technicians/Specialists:
" . json_encode($technicians, JSON_PRETTY_PRINT) . "

Additional instructions: {$customInstructions}

YOUR GOAL:
1. Greet the customer and answer questions about services, prices, or appointment availability.
2. If the customer indicates they want to book, or mentions a problem/symptom (e.g., 'need haircut', 'leaking sink', 'oil change'), recommend the best matching service(s) from the catalog.
3. If they mention a date (e.g., 'tomorrow', 'next Monday', 'Saturday') or time (e.g., 'afternoon', '2pm'), convert it to YYYY-MM-DD and HH:MM format.
4. If they mention home service or on-site, set mode to 'home_service', otherwise 'in_shop'.
5. Return your response in STRICT JSON format with this exact structure:
{
  \"message\": \"Your friendly, helpful message to the customer\",
  \"suggested_actions\": {
    \"mode\": \"in_shop\" or \"home_service\",
    \"service_ids\": [list of integer service IDs that match],
    \"technician_id\": integer or null,
    \"preferred_date\": \"YYYY-MM-DD\" or null,
    \"preferred_time\": \"HH:MM\" or null
  }
}
Respond ONLY with the raw JSON object, without markdown code fences or backticks.";

        try {
            $contents = [];
            foreach ($conversationHistory as $msg) {
                $role = ($msg['sender'] ?? 'user') === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role'  => $role,
                    'parts' => [['text' => $msg['text'] ?? '']],
                ];
            }
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            $response = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature'       => 0.2,
                    'maxOutputTokens'   => 800,
                    'responseMimeType'  => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                $parsed = json_decode($rawText, true);

                if (is_array($parsed) && isset($parsed['message'])) {
                    return [
                        'success'           => true,
                        'message'           => $parsed['message'],
                        'suggested_actions' => $parsed['suggested_actions'] ?? null,
                    ];
                }
            }

            Log::warning("Gemini API non-200 or unexpected output: " . $response->body());
            return $this->smartFallback($userMessage, $company, $services, $assistantName);
        } catch (\Exception $e) {
            Log::warning("Gemini API call exception: " . $e->getMessage());
            return $this->smartFallback($userMessage, $company, $services, $assistantName);
        }
    }

    /**
     * Resilient smart rule-based matcher when Gemini API key is missing or network fails.
     */
    protected function smartFallback(string $userMessage, Company $company, array $services, string $assistantName): array
    {
        $lower = strtolower($userMessage);
        $matchedServices = [];

        foreach ($services as $srv) {
            $srvName = strtolower($srv['name']);
            $catName = strtolower($srv['category']);
            if (str_contains($lower, $srvName) || str_contains($srvName, $lower) || str_contains($lower, $catName)) {
                $matchedServices[] = $srv;
            }
        }

        $companyName = $company->company_name ?: 'Our Service Center';

        if (!empty($matchedServices)) {
            $first = $matchedServices[0];
            $srvNames = implode(', ', array_column(array_slice($matchedServices, 0, 3), 'name'));
            return [
                'success'           => true,
                'message'           => "I found the matching service: **{$srvNames}** (approx. {$first['duration']} at \${$first['price']}). I have highlighted this for you below. Please pick your preferred date & time to book!",
                'suggested_actions' => [
                    'mode'         => str_contains($lower, 'home') ? 'home_service' : 'in_shop',
                    'service_ids'  => array_column(array_slice($matchedServices, 0, 3), 'id'),
                    'preferred_date' => str_contains($lower, 'tomorrow') ? now()->addDay()->format('Y-m-d') : now()->format('Y-m-d'),
                ],
            ];
        }

        return [
            'success'           => true,
            'message'           => "Hello! I am your {$assistantName} for {$companyName}. How can I assist you today? You can browse our services below, or tell me what issue or service you are looking for!",
            'suggested_actions' => null,
        ];
    }
}
