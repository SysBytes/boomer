<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\Work;
use App\Models\User;
use App\Models\Client;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
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

        // ¿Tiene un check-in sin check-out?
        $activeLog = TimeLog::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->with('work.location.client')
            ->latest('check_in_time')
            ->first();

        // Proyectos disponibles según rol (solo necesarios para check-in)
        $works = $this->getAvailableWorks($user);

        return view('backoffice.dashboard.checkin', compact('activeLog', 'works'));
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

        $request->validate([
            'work_id' => 'required|integer|exists:works,work_id',
            'check_in_photo' => 'required|string',   // base64
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Verificar que el proyecto esté disponible para este usuario
        $availableWorkIds = $this->getAvailableWorks($user)->pluck('work_id');
        if (!$availableWorkIds->contains($request->work_id)) {
            return back()->with('error', 'No tienes permiso para hacer check-in en este proyecto.');
        }

        $work = Work::with('location')->findOrFail($request->work_id);

        if (!$work->location) {
            return back()->with('error', 'El proyecto no tiene una locación asignada.');
        }

        $distance = $this->calculateDistance($work->location->location_lat, $work->location->location_lng, $request->latitude, $request->longitude);

        if ($distance !== null && $distance > 100) {
            return back()->with('error', "Estás demasiado lejos de la obra para hacer Check-In. Distancia actual: {$distance} metros. (Máximo permitido: 100m)");
        }

        $now = now();

        // Crear el registro primero para obtener el ID
        $log = TimeLog::create([
            'user_id' => $user->id,
            'work_id' => $request->work_id,
            'check_in_time' => $now,
            'check_in_photo' => '', // temporal, se actualiza tras guardar imagen
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
        ]);

        // Guardar imagen
        $photoPath = $this->saveBase64Image(
            base64Data: $request->check_in_photo,
            timeLogId: $log->time_log_id,
            workId: $request->work_id,
            userId: $user->id,
            timestamp: $now,
            type: 'in'
        );

        $log->update(['check_in_photo' => $photoPath]);

        return redirect()->route('checkin.index')
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

        $work = $log->work;
        $work->load('location');

        if (!$work->location) {
            return back()->with('error', 'El proyecto no tiene una locación asignada.');
        }

        $distance = $this->calculateDistance($work->location->location_lat, $work->location->location_lng, $request->latitude, $request->longitude);

        if ($distance !== null && $distance > 100) {
            return back()->with('error', "Estás demasiado lejos de la obra para hacer Check-Out. Distancia actual: {$distance} metros. (Máximo permitido: 100m)");
        }

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

        return redirect()->route('checkin.index')
            ->with('success', "Check-Out registrado. Horas trabajadas: {$log->fresh()->hours_worked_formatted}");
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Proyectos disponibles según rol del usuario.
     */
    private function getAvailableWorks($user)
    {
        $query = Work::where('active', 1)->with('location.client');

        if ($user->hasAnyRole(['technician', 'assistant'])) {
            $works = $query->whereHas('users', fn($q) => $q->where('users.id', $user->id))->get();
        } elseif ($user->hasRole('supervisor')) {
            $defaultStaffProject = env('DEFAULT_STAFF_PROJECT');
            $works = $query->where(function ($q) use ($user, $defaultStaffProject) {
                $q->where('supervisor_id', $user->id);
                if ($defaultStaffProject) {
                    $q->orWhere('works.work_id', $defaultStaffProject);
                }
            })->get();
        } else {
            $works = $query->get(); // admin, developer, purchasing, etc.
        }

        return $works->map(function ($work) {
            if ($work->location) {
                $work->location->location_name = mb_strtoupper($work->location->location_name, 'UTF-8');
            }
            $work->work_name = mb_strtoupper($work->work_name, 'UTF-8');
            return $work;
        })->sortBy(function ($work) {
            return ($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? '');
        });
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

    // ──────────────────────────────────────────────
    // Historial de Horas (Backoffice)
    // ──────────────────────────────────────────────
    private function getCountries()
    {
        return [
            1 => 'Colombia',
            2 => 'Argentina',
        ];
    }

    public function history(Request $request)
    {
        $query = TimeLog::with(['user', 'work.location.client']);

        // Filtro por Técnico
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por País
        if ($request->filled('country_id')) {
            $query->whereHas('work.location.client', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        // Filtro por Cliente
        if ($request->filled('client_id')) {
            $query->whereHas('work.location.client', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        // Filtro por Proyecto
        if ($request->filled('work_id')) {
            $query->where('work_id', $request->work_id);
        }

        // Filtro por Ciudad
        if ($request->filled('work_city')) {
            $query->whereHas('work.location', function ($q) use ($request) {
                $q->where('location_city', $request->work_city);
            });
        }

        // Filtro por Fecha Inicio (check_in_time)
        if ($request->filled('start_date')) {
            $query->whereDate('check_in_time', '>=', $request->start_date);
        }

        // Filtro por Fecha Fin (check_out_time)
        if ($request->filled('end_date')) {
            $query->whereDate('check_out_time', '<=', $request->end_date);
        }

        $timelogs = $query->orderBy('check_in_time', 'desc')->get();

        $users = User::with('roles')->whereHas('timeLogs')->orderBy('name', 'asc')->get();
        $clients = Client::where('active', 1)->orderBy('client_name', 'asc')->get();
        $works = Work::where('active', 1)->with('location.client')->get()->sortBy(function ($work) {
            return strtolower(($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? ''));
        });
        $cities = Location::where('active', 1)->whereNotNull('location_city')->where('location_city', '!=', '')->distinct()->orderBy('location_city', 'asc')->pluck('location_city');
        $countries = $this->getCountries();
        asort($countries);

        return view('backoffice.timelog.index', compact('timelogs', 'users', 'clients', 'works', 'cities', 'countries'));
    }

    // ──────────────────────────────────────────────
    // Registro Manual (Backoffice)
    // ──────────────────────────────────────────────
    public function manualCreate()
    {
        $users = User::with([
            'works.location.client',
            'supervisedWorks.location.client',
            'roles',
            'timeLogs' => function ($q) {
                $q->whereNull('check_out_time')->with('work.location.client');
            }
        ])->whereHas('roles', function ($q) {
            $q->whereIn('slug', ['technician', 'assistant', 'supervisor', 'staff']);
        })->where('active', 1)->orderBy('name', 'asc')->get();

        // Get default staff project
        $defaultStaffWorkId = env('DEFAULT_STAFF_PROJECT');
        if (empty($defaultStaffWorkId)) {
            $path = base_path('.env');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                if (preg_match('/^DEFAULT_STAFF_PROJECT=(.*)$/m', $content, $matches)) {
                    $defaultStaffWorkId = trim($matches[1]);
                }
            }
        }

        $defaultStaffWork = null;
        if (!empty($defaultStaffWorkId)) {
            $defaultStaffWork = Work::where('work_id', $defaultStaffWorkId)->where('active', 1)->with('location.client')->first();
        }

        return view('backoffice.timelog.manual', compact('users', 'defaultStaffWork'));
    }

    public function manualStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_id' => 'required|exists:works,work_id',
            'action' => 'required|in:checkin,checkout',
            'record_time' => 'required|date',
            'observation' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);
        $now = $request->record_time;
        $adminId = Auth::id();

        if ($request->action === 'checkin') {
            // Verificar si hay uno abierto
            $existing = TimeLog::where('user_id', $user->id)
                ->whereNull('check_out_time')
                ->first();

            if ($existing) {
                return back()->with('error', 'El usuario ya tiene un check-in abierto.');
            }

            TimeLog::create([
                'user_id' => $user->id,
                'work_id' => $request->work_id,
                'check_in_time' => $now,
                'check_in_photo' => 'img/default-checkin.png',
                'check_in_lat' => 0, // o las del work
                'check_in_lng' => 0, // o las del work
                'check_in_observation' => $request->observation,
                'check_in_registered_by' => $adminId,
            ]);

            return redirect()->back()->with('success', 'Check-In manual registrado correctamente.');

        } else {
            // Check-out manual
            $log = TimeLog::where('user_id', $user->id)
                ->where('work_id', $request->work_id)
                ->whereNull('check_out_time')
                ->latest('check_in_time')
                ->first();

            if (!$log) {
                return back()->with('error', 'El usuario no tiene un check-in abierto en este proyecto.');
            }

            $checkInTime = \Carbon\Carbon::parse($log->check_in_time);
            $recordTime = \Carbon\Carbon::parse($now);

            if ($recordTime->lessThan($checkInTime)) {
                return back()->with('error', 'La fecha/hora de check-out no puede ser anterior al check-in.');
            }

            $hoursWorked = $checkInTime->diffInMinutes($recordTime) / 60;

            $log->update([
                'check_out_time' => $recordTime,
                'check_out_photo' => 'img/default-checkout.png',
                'check_out_lat' => 0,
                'check_out_lng' => 0,
                'hours_worked' => round($hoursWorked, 2),
                'check_out_observation' => $request->observation,
                'check_out_registered_by' => $adminId,
            ]);

            // Calcular horas extras
            \App\Services\OvertimeCalculator::calculateForTimeLog($log);

            return redirect()->back()->with('success', 'Check-Out manual registrado correctamente.');
        }
    }

    // ──────────────────────────────────────────────
    // Detalles del Registro (Backoffice)
    // ──────────────────────────────────────────────
    public function show(TimeLog $timeLog)
    {
        $timeLog->load(['user', 'work.location.client', 'checkInAdmin', 'checkOutAdmin']);

        $workLat = $timeLog->work->location->location_lat ?? null;
        $workLng = $timeLog->work->location->location_lng ?? null;

        $inLat = $timeLog->check_in_lat;
        $inLng = $timeLog->check_in_lng;

        $outLat = $timeLog->check_out_lat;
        $outLng = $timeLog->check_out_lng;

        $inDistance = $this->calculateDistance($workLat, $workLng, $inLat, $inLng);
        $outDistance = $this->calculateDistance($workLat, $workLng, $outLat, $outLng);

        return view('backoffice.timelog.show', compact('timeLog', 'inDistance', 'outDistance'));
    }

    /**
     * Calcula la distancia en metros entre dos coordenadas usando la fórmula de Haversine.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return null;
        }

        $earthRadius = 6371000; // Radio de la tierra en metros

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c); // Distancia en metros
    }
    // ──────────────────────────────────────────────
    // Editar Registro de Horas
    // ──────────────────────────────────────────────
    public function edit(TimeLog $timeLog)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
            abort(403, 'Acceso no autorizado.');
        }

        if (empty($timeLog->check_out_time)) {
            return redirect()->route('timelogs.index')->with('error', 'Solo se pueden editar registros que ya tengan horario de salida (Check-Out).');
        }

        $timeLog->load(['user', 'work.location.client']);
        return view('backoffice.timelog.edit', compact('timeLog'));
    }

    public function update(Request $request, TimeLog $timeLog)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
            abort(403, 'Acceso no autorizado.');
        }

        if (empty($timeLog->check_out_time)) {
            return redirect()->route('timelogs.index')->with('error', 'Solo se pueden editar registros que ya tengan horario de salida (Check-Out).');
        }

        $request->validate([
            'check_in_time' => 'required|date',
            'check_out_time' => 'nullable|date|after_or_equal:check_in_time',
        ]);

        $checkInTime = \Carbon\Carbon::parse($request->check_in_time);

        $hoursWorked = null;
        $checkOutTime = null;
        if ($request->check_out_time) {
            $checkOutTime = \Carbon\Carbon::parse($request->check_out_time);
            $hoursWorked = $checkInTime->diffInMinutes($checkOutTime) / 60;
        }

        $timeLog->update([
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'hours_worked' => $hoursWorked !== null ? round($hoursWorked, 2) : null,
        ]);

        // Recalcular horas extras si hay check_out, si no hay (o se quitó), borrar extras previas.
        if ($timeLog->check_out_time) {
            \App\Services\OvertimeCalculator::calculateForTimeLog($timeLog);
        } else {
            \App\Models\Overtime::where('time_log_id', $timeLog->time_log_id)->delete();
        }

        return redirect()->route('timelogs.index')
            ->with('success', 'Registro de horas actualizado y horas extras recalculadas exitosamente.');
    }

    // ──────────────────────────────────────────────
    // Eliminar Registro de Horas
    // ──────────────────────────────────────────────
    public function destroy(TimeLog $timeLog)
    {
        if (!auth()->user()->hasRole('developer')) {
            abort(403, 'Acceso no autorizado.');
        }

        $timeLog->delete();

        return redirect()->route('timelogs.index')
            ->with('success', 'Registro de horas eliminado exitosamente.');
    }

    // ──────────────────────────────────────────────
    // MÉTODOS PARA RECORDATORIO WHATSAPP
    // ──────────────────────────────────────────────
    public function remind(TimeLog $timeLog)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
            abort(403, 'Acceso no autorizado.');
        }

        if (!empty($timeLog->check_out_time)) {
            return redirect()->route('timelogs.index')->with('error', 'El usuario ya ha realizado su Check-Out.');
        }

        $now = \Carbon\Carbon::now();
        $checkInTime = \Carbon\Carbon::parse($timeLog->check_in_time);
        $hoursElapsed = round($checkInTime->diffInMinutes($now) / 60, 1);

        if ($hoursElapsed < 9) {
            return redirect()->route('timelogs.index')->with('error', 'El registro aún no supera las 9 horas permitidas.');
        }

        $user = $timeLog->user;
        if (!$user || !$user->phone) {
            return redirect()->route('timelogs.index')->with('error', 'El usuario no tiene un número de teléfono registrado.');
        }

        $projectName = $timeLog->work ? ($timeLog->work->location->location_name ?? '') . ' - ' . $timeLog->work->work_name : 'Desconocido';
        $isStaff = $user->hasRole('staff');
        $checkoutLink = $isStaff ? url('/dashboard/staff-checkin') : url('/dashboard/checkin');

        $components = [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $user->name],
                    ['type' => 'text', 'text' => (string) $hoursElapsed],
                    ['type' => 'text', 'text' => $projectName],
                    ['type' => 'text', 'text' => $checkoutLink],
                ]
            ]
        ];

        $notificationMethod = env('NOTIFICATION_METHOD', 'whatsapp');

        if ($notificationMethod === 'sms') {
            $smsMessage = "BOOMER ELECOMM: Hola {$user->name}, llevas más de {$hoursElapsed} horas trabajando en la obra {$projectName}. Recuerda registrar tu hora de salida (Check-Out) en el siguiente enlace: {$checkoutLink} (Si aún te encuentras en la obra, omite este mensaje).";
            $result = \App\Services\SmsService::sendSms($user, $smsMessage);
            $channelName = 'SMS';
        } else {
            $result = \App\Services\WhatsAppService::sendTemplateMessage($user, 'checkout_reminder', $components);
            $channelName = 'WhatsApp';
        }

        if ($result === true) {
            return redirect()->route('timelogs.index')->with('success', "Recordatorio de {$channelName} enviado al usuario exitosamente.");
        } else {
            return redirect()->route('timelogs.index')->with('error', "Error de {$channelName}: " . $result);
        }
    }
}
