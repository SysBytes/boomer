<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffCheckInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ──────────────────────────────────────────────
    // Pantalla principal: detecta si hay check-in activo
    // ──────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        // Validar DEFAULT_STAFF_PROJECT
        $defaultWorkId = $this->getDefaultWorkId();
        $configError = false;
        $defaultWork = null;

        if (!$defaultWorkId) {
            $configError = true;
        } else {
            $defaultWork = Work::where('work_id', $defaultWorkId)->where('active', 1)->with('location.client')->first();
            if (!$defaultWork) {
                $configError = true;
            }
        }

        // ¿Tiene un check-in sin check-out?
        $activeLog = TimeLog::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->with('work.location.client')
            ->latest('check_in_time')
            ->first();

        return view('backoffice.dashboard.staff_checkin', compact('activeLog', 'configError', 'defaultWork'));
    }

    // ──────────────────────────────────────────────
    // Registro de Check-In
    // ──────────────────────────────────────────────
    public function checkIn(Request $request)
    {
        $user = Auth::user();

        // Bloquear si ya tiene un check-in activo
        $existing = TimeLog::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->first();

        if ($existing) {
            return back()->with('error', 'Ya tienes un check-in activo. Debes hacer check-out antes de iniciar uno nuevo.');
        }

        // Validar que DEFAULT_STAFF_PROJECT esté configurado
        $defaultWorkId = $this->getDefaultWorkId();
        if (!$defaultWorkId) {
            return back()->with('error', 'El proyecto por defecto para staff no está configurado. Contacta al administrador.');
        }

        $work = Work::where('work_id', $defaultWorkId)->where('active', 1)->with('location.client')->first();
        if (!$work) {
            return back()->with('error', 'El proyecto por defecto configurado no es válido. Contacta al administrador.');
        }

        $request->validate([
            'check_in_photo' => 'required|string',   // base64
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $now = now();

        // Crear el registro
        $log = TimeLog::create([
            'user_id' => $user->id,
            'work_id' => $work->work_id,
            'check_in_time' => $now,
            'check_in_photo' => '', // temporal, se actualiza tras guardar imagen
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
        ]);

        // Guardar imagen
        $photoPath = $this->saveBase64Image(
            base64Data: $request->check_in_photo,
            timeLogId: $log->time_log_id,
            workId: $work->work_id,
            userId: $user->id,
            timestamp: $now,
            type: 'in'
        );

        $log->update(['check_in_photo' => $photoPath]);

        return redirect()->route('staff.checkin.index')
            ->with('success', 'Check-In registrado correctamente. ¡Buen trabajo!');
    }

    // ──────────────────────────────────────────────
    // Registro de Check-Out
    // ──────────────────────────────────────────────
    public function checkOut(Request $request)
    {
        $user = Auth::user();

        $log = TimeLog::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->latest('check_in_time')
            ->first();

        if (!$log) {
            return back()->with('error', 'No tienes un check-in activo.');
        }

        $request->validate([
            'check_out_photo' => 'required|string',  // base64
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $now = now();

        // Guardar imagen de check-out
        $photoPath = $this->saveBase64Image(
            base64Data: $request->check_out_photo,
            timeLogId: $log->time_log_id,
            workId: $log->work_id,
            userId: $user->id,
            timestamp: $now,
            type: 'out'
        );

        // Calcular horas trabajadas
        $checkInTime = $log->check_in_time;
        $hoursWorked = $checkInTime->diffInMinutes($now) / 60;

        $log->update([
            'check_out_time' => $now,
            'check_out_photo' => $photoPath,
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
            'hours_worked' => round($hoursWorked, 2),
        ]);

        // Calcular horas extras
        \App\Services\OvertimeCalculator::calculateForTimeLog($log);

        return redirect()->route('staff.checkin.index')
            ->with('success', "Check-Out registrado. Horas trabajadas: {$log->fresh()->hours_worked_formatted}");
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Obtiene el work_id por defecto para staff desde el .env
     */
    private function getDefaultWorkId()
    {
        $value = env('DEFAULT_STAFF_PROJECT');

        if (empty($value)) {
            // Leer directamente del archivo .env por si no está cacheado
            $path = base_path('.env');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                if (preg_match('/^DEFAULT_STAFF_PROJECT=(.*)$/m', $content, $matches)) {
                    $value = trim($matches[1]);
                }
            }
        }

        return !empty($value) ? (int) $value : null;
    }

    /**
     * Guarda imagen base64, crea thumbnail y comprime.
     * Retorna la ruta relativa (desde public/).
     *
     * Nomenclatura: {type}_{timeLogId}_{workCode}_{userId}_{YYYYMMDDHHmmss}.jpg
     */
    private function saveBase64Image(
        string $base64Data,
        int $timeLogId,
        int $workId,
        int $userId,
        object $timestamp,
        string $type        // 'in' | 'out'
    ): string {
        // Limpiar el prefijo data URI si viene incluido
        $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($base64Data);

        $dir = base_path('../img/check_in_out');
        $thumbDir = "{$dir}/thumbs";

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $workCode = str_pad($workId, 4, '0', STR_PAD_LEFT);
        $datePart = $timestamp->format('YmdHis');
        $filename = "ci_{$type}_{$timeLogId}_{$workCode}_{$userId}_{$datePart}.jpg";
        $thumbName = "thumb_{$type}_{$timeLogId}_{$workCode}_{$userId}_{$datePart}.jpg";

        $fullPath = "{$dir}/{$filename}";
        $thumbPath = "{$thumbDir}/{$thumbName}";

        // Cargar imagen desde binario
        $srcImage = imagecreatefromstring($imageData);
        if ($srcImage === false) {
            // Si falla la creación (formato no soportado), guardar tal cual
            file_put_contents($fullPath, $imageData);
            return "img/check_in_out/{$filename}";
        }

        $origW = imagesx($srcImage);
        $origH = imagesy($srcImage);

        // ── Guardar imagen principal comprimida al 75% ──
        imagejpeg($srcImage, $fullPath, 75);

        // ── Crear thumbnail (máx 300px) ──
        $maxDim = 300;
        $ratio = min($maxDim / $origW, $maxDim / $origH, 1); // nunca ampliar
        $thumbW = (int) round($origW * $ratio);
        $thumbH = (int) round($origH * $ratio);

        $thumbImg = imagecreatetruecolor($thumbW, $thumbH);
        imagecopyresampled($thumbImg, $srcImage, 0, 0, 0, 0, $thumbW, $thumbH, $origW, $origH);
        imagejpeg($thumbImg, $thumbPath, 75);

        imagedestroy($srcImage);
        imagedestroy($thumbImg);

        return "img/check_in_out/{$filename}";
    }
}
