<?php

namespace App\Services;

use App\Models\CorporateContent;
use App\Models\User;
use App\Models\UserInductionEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InductionEvidenceService
{
    /**
     * Valida y almacena en disco privado la fotografía (obligatoria) y firma digital (opcional),
     * registrando la evidencia en user_induction_evidences.
     */
    public function storeEvidence(Request $request, User $user, ?CorporateContent $content = null): UserInductionEvidence
    {
        $request->validate([
            'accept_terms' => 'accepted',
            'evidence_photo_base64' => 'required|string',
            'signature_base64' => 'required|string|max:3145728',
        ], [
            'accept_terms.accepted' => 'Debes aceptar la declaración de cumplimiento.',
            'evidence_photo_base64.required' => 'Debes tomar una fotografía selfie en tiempo real con tu cámara.',
            'signature_base64.required' => 'Debes dibujar tu firma digital antes de continuar.',
            'signature_base64.max' => 'La firma digital excede el tamaño máximo permitido.',
        ]);

        // Decodificar ambas imágenes antes de escribir en disco para evitar archivos huérfanos si falla validación.
        $decodedPhoto = $this->decodeBase64Image(
            trim((string) $request->input('evidence_photo_base64')),
            'evidence_photo_base64',
            'La fotografía capturada no tiene un formato de imagen válido.',
            'No se pudo procesar la fotografía de evidencia.'
        );

        $decodedSignature = $this->decodeBase64Image(
            trim((string) $request->input('signature_base64')),
            'signature_base64',
            'La firma digital no tiene un formato de imagen válido.',
            'No se pudo procesar la firma digital.',
            2 * 1024 * 1024
        );

        $photoPath = sprintf(
            'induction/evidences/user_%d_%s_%s.%s',
            $user->id,
            now()->format('Ymd_His'),
            bin2hex(random_bytes(4)),
            $decodedPhoto['ext']
        );

        Storage::disk('local')->put($photoPath, $decodedPhoto['binary']);

        $signaturePath = sprintf(
            'induction/signatures/user_%d_%s_%s.%s',
            $user->id,
            now()->format('Ymd_His'),
            bin2hex(random_bytes(4)),
            $decodedSignature['ext']
        );

        Storage::disk('local')->put($signaturePath, $decodedSignature['binary']);

        return UserInductionEvidence::create([
            'user_id' => $user->id,
            'content_id' => $content?->id,
            'evidence_photo_path' => $photoPath,
            'signature_path' => $signaturePath,
            'ip_address' => $request->ip(),
            'accepted_at' => now(),
        ]);
    }

    /**
     * Valida y decodifica una imagen Data URL (JPEG, PNG o WEBP).
     *
     * @return array{binary: string, ext: string}
     */
    private function decodeBase64Image(
        string $rawDataUrl,
        string $field,
        string $invalidFormatMsg,
        string $decodeErrorMsg,
        int $maxBytes = 10485760
    ): array {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,([A-Za-z0-9+\/=\s]+)$/i', $rawDataUrl, $matches)) {
            throw ValidationException::withMessages([
                $field => [$invalidFormatMsg],
            ]);
        }

        $base64Body = preg_replace('/\s+/', '', $matches[2]);
        $binary = base64_decode($base64Body, true);
        if ($binary === false || strlen($binary) < 32 || strlen($binary) > $maxBytes) {
            throw ValidationException::withMessages([
                $field => [$decodeErrorMsg],
            ]);
        }

        $ext = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);

        return [
            'binary' => $binary,
            'ext' => $ext,
        ];
    }
}
