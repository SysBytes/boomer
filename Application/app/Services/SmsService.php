<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envía un SMS utilizando la API de LabsMobile (JSON POST).
     *
     * @param string $to
     * @param string $message
     * @return bool|string Devuelve true si fue exitoso, o un string con el error.
     */
    public static function sendSms($user, $message)
    {
        $username = env('LABSMOBILE_USERNAME');
        $token = env('LABSMOBILE_TOKEN');

        if (!$username || !$token) {
            Log::error('LabsMobile API credentials are not set in .env');
            return 'Faltan credenciales de LabsMobile en .env (LABSMOBILE_USERNAME y LABSMOBILE_TOKEN).';
        }

        $to = $user->phone;
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        if (empty($cleanTo)) {
            Log::warning("Intento de envío de SMS a un número vacío o inválido: '{$to}'");
            return 'Número de teléfono inválido.';
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

        $payload = [
            'message' => $message,
            'tpoa' => 'Boomer', // Remitente por defecto (hasta 11 caracteres)
            'recipient' => [
                ['msisdn' => $cleanTo]
            ]
        ];

        try {
            $response = Http::withBasicAuth($username, $token)
                ->withHeaders([
                    'Cache-Control' => 'no-cache',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.labsmobile.com/json/send', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                // LabsMobile devuelve code "0" si todo fue exitoso
                if (isset($responseData['code']) && $responseData['code'] == '0') {
                    Log::info("SMS enviado exitosamente a {$cleanTo}.");
                    return true;
                }
            }

            // Si hay error
            $errorData = $response->json();
            $errorMsg = $errorData['message'] ?? 'Error desconocido de la API';
            
            Log::error('LabsMobile API Error', [
                'to' => $cleanTo,
                'status' => $response->status(),
                'response' => $errorData
            ]);

            return $errorMsg;

        } catch (\Exception $e) {
            Log::error('Excepción al enviar SMS: ' . $e->getMessage());
            return 'Error de conexión con proveedor SMS.';
        }
    }
}
