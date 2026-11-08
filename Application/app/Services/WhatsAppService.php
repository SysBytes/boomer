<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a template message using Meta WhatsApp Cloud API.
     *
     * @param string $to Phone number (will be cleaned to numeric only)
     * @param string $templateName Name of the template to send
     * @param array $components Components array (usually body parameters)
     * @return bool True if successful, False otherwise
     */
    public static function sendTemplateMessage($user, $templateName, $components = [], $langCode = 'es')
    {
        $apiUrl = env('WHATSAPP_API_URL', 'https://graph.facebook.com/v20.0/');
        $phoneId = env('WHATSAPP_PHONE_ID');
        $token = env('WHATSAPP_ACCESS_TOKEN');

        if (!$phoneId || !$token) {
            Log::error('WhatsApp API credentials are not set in .env');
            return false;
        }

        $to = $user->phone;
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        if (empty($cleanTo)) {
            Log::warning("Intento de envío de WhatsApp a un número vacío o inválido: '{$to}'");
            return false;
        }

        // Si el número tiene longitud local, intentamos agregarle el código de país del usuario
        if (strlen($cleanTo) >= 7 && strlen($cleanTo) <= 10) {
            $countryCode = '57'; // Default Colombia
            // Get user's primary country code if relation is loaded or exists
            if ($user->relationLoaded('countries') && $user->countries->isNotEmpty()) {
                $firstCountry = $user->countries->first();
                if ($firstCountry) {
                    $countryCode = preg_replace('/[^0-9]/', '', $firstCountry->phone_code);
                }
            } else {
                // Relación no cargada, intentar cargar solo el primero
                $firstCountry = $user->countries()->first();
                if ($firstCountry) {
                    $countryCode = preg_replace('/[^0-9]/', '', $firstCountry->phone_code);
                }
            }
            $cleanTo = $countryCode . $cleanTo;
        }

        $endpoint = rtrim($apiUrl, '/') . '/' . $phoneId . '/messages';

        if ($templateName === 'sample_issue_resolution' || $templateName === 'hello_world') {
            $langCode = 'en_US'; // Override for default Meta templates if requested without custom lang
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $cleanTo,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $langCode
                ]
            ]
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        try {
            $response = Http::withToken($token)->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp enviado exitosamente a {$cleanTo} usando plantilla {$templateName}.");
                return true;
            }

            $errorResponse = $response->json();
            Log::error('WhatsApp API Error', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'status' => $response->status(),
                'response' => $errorResponse
            ]);

            return $errorResponse['error']['message'] ?? 'Error de la API (' . $response->status() . ')';

        } catch (\Exception $e) {
            Log::error('WhatsApp Exception: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
}
