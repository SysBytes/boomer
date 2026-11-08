<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    /**
     * Webhook verification for Meta App.
     */
    public function verify(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'MySecretVerifyToken123');

        $mode = $request->input('hub_mode', $request->input('hub.mode'));
        $token = $request->input('hub_verify_token', $request->input('hub.verify_token'));
        $challenge = $request->input('hub_challenge', $request->input('hub.challenge'));

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
            }
            return response('Forbidden', 403);
        }

        return response('Bad Request', 400);
    }

    /**
     * Webhook payload receiver (for future use to process incoming messages/status).
     */
    public function handle(Request $request)
    {
        // Parse payload from WhatsApp
        // For now, just return 200 OK so Meta doesn't retry
        return response()->json(['status' => 'success'], 200);
    }
}
