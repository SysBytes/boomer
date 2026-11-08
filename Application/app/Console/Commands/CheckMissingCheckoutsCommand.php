<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimeLog;
use App\Models\Role;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckMissingCheckoutsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-missing-checkouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for users who have not checked out after 9 hours and sends a WhatsApp reminder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enabled = env('WHATSAPP_NOTIFICATIONS_ENABLED', 'true');
        if ($enabled === 'false' || $enabled === false) {
            $this->info('WhatsApp/SMS notifications are disabled in settings. Skipping process.');
            return;
        }

        $now = Carbon::now();
        $nineHoursAgo = $now->copy()->subHours(9);

        // Find active timelogs older than 9 hours
        $timeLogs = TimeLog::with(['user', 'work.location'])
            ->whereNull('check_out_time')
            ->where('check_in_time', '<=', $nineHoursAgo)
            ->get();

        if ($timeLogs->isEmpty()) {
            $this->info('No pending checkouts older than 9 hours found.');
            return;
        }

        foreach ($timeLogs as $log) {
            $user = $log->user;

            if (!$user) {
                continue; 
            }

            $checkInTime = Carbon::parse($log->check_in_time);
            $hoursElapsed = round($checkInTime->diffInMinutes($now) / 60, 1);
            $projectName = $log->work ? ($log->work->location->location_name ?? '') . ' - ' . $log->work->work_name : 'Desconocido';

            $isStaff = $user->hasRole('staff');
            $checkoutLink = $isStaff ? url('/dashboard/staff-checkin') : url('/dashboard/checkin');

            $notifiedPhones = [];

            // 1. Send Reminder to the User
            if ($user->phone) {
                $this->sendReminderToUser($user, $hoursElapsed, $projectName, $checkoutLink, $notifiedPhones);
            }

            // 2. Send Alert to Supervisor or Admins
            if ($isStaff || ($log->work && $user->id == $log->work->supervisor_id)) {
                $this->alertAdmins($user, $hoursElapsed, $projectName, $notifiedPhones);
            } else {
                $alerted = $this->alertSupervisor($log->work, $user, $hoursElapsed, $projectName, $notifiedPhones);
                if (!$alerted) {
                    $this->alertAdmins($user, $hoursElapsed, $projectName, $notifiedPhones);
                }
            }
        }

        $this->info('Checkouts notification process completed.');
    }

    private function sendReminderToUser($user, $hoursElapsed, $projectName, $checkoutLink, &$notifiedPhones)
    {
        if ($this->isHolidayForUser($user)) {
            return;
        }

        if (in_array($user->phone, $notifiedPhones)) {
            return;
        }

        // Template: checkout_reminder
        // Vars: {{1}} Name, {{2}} Hours, {{3}} Project, {{4}} Link
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
            \App\Services\SmsService::sendSms($user, $smsMessage);
        } else {
            $result = WhatsAppService::sendTemplateMessage($user, 'checkout_reminder', $components);
            if ($result !== true) {
                // Fallback a SMS si WhatsApp falla (ej. límite de presupuesto o error de API)
                $smsMessage = "BOOMER ELECOMM: Hola {$user->name}, llevas más de {$hoursElapsed} horas trabajando en la obra {$projectName}. Recuerda registrar tu hora de salida (Check-Out) en el siguiente enlace: {$checkoutLink} (Si aún te encuentras en la obra, omite este mensaje).";
                \App\Services\SmsService::sendSms($user, $smsMessage);
                Log::warning("WhatsApp falló para el usuario {$user->id}. SMS de respaldo enviado.");
            }
        }

        $notifiedPhones[] = $user->phone;
    }

    private function alertAdmins($user, $hoursElapsed, $projectName, &$notifiedPhones)
    {
        // Notify all admins, but exclude the user themselves if they are an admin
        $admins = User::with('countries')->whereHas('roles', function ($q) {
            $q->where('slug', 'admin');
        })->where('id', '!=', $user->id)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        foreach ($admins as $admin) {
            $this->sendAlertTo($admin, $user->name, $hoursElapsed, $projectName, $notifiedPhones);
        }
    }

    private function alertSupervisor($work, $user, $hoursElapsed, $projectName, &$notifiedPhones)
    {
        if (!$work || !$work->supervisor_id) {
            return false;
        }

        $supervisor = User::with('countries')->find($work->supervisor_id);
        if ($supervisor && $supervisor->phone) {
            $this->sendAlertTo($supervisor, $user->name, $hoursElapsed, $projectName, $notifiedPhones);
            return true;
        }

        return false;
    }

    private function sendAlertTo($recipientUser, $userName, $hoursElapsed, $projectName, &$notifiedPhones)
    {
        if ($this->isHolidayForUser($recipientUser)) {
            return;
        }

        $phone = $recipientUser->phone;
        if (in_array($phone, $notifiedPhones)) {
            return;
        }

        // Template: checkout_alert
        // Vars: {{1}} User Name, {{2}} Hours, {{3}} Project
        $components = [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $userName],
                    ['type' => 'text', 'text' => (string) $hoursElapsed],
                    ['type' => 'text', 'text' => $projectName],
                ]
            ]
        ];

        $notificationMethod = env('NOTIFICATION_METHOD', 'whatsapp');

        if ($notificationMethod === 'sms') {
            $smsMessage = "BOOMER ELECOMM: El usuario {$userName} lleva más de {$hoursElapsed} horas registrado en la obra {$projectName}. Por favor, valida si aun se encuentra en la obra.";
            \App\Services\SmsService::sendSms($recipientUser, $smsMessage);
        } else {
            $result = WhatsAppService::sendTemplateMessage($recipientUser, 'checkout_alert', $components);
            if ($result !== true) {
                // Fallback a SMS si WhatsApp falla
                $smsMessage = "BOOMER ELECOMM: El usuario {$userName} lleva más de {$hoursElapsed} horas registrado en la obra {$projectName}. Por favor, valida si aun se encuentra en la obra.";
                \App\Services\SmsService::sendSms($recipientUser, $smsMessage);
                Log::warning("WhatsApp falló para el supervisor/admin {$recipientUser->id}. SMS de respaldo enviado.");
            }
        }

        $notifiedPhones[] = $phone;
    }

    private function isHolidayForUser($user)
    {
        if (!$user->relationLoaded('countries')) {
            $user->load('countries');
        }

        $countryIds = $user->countries->pluck('id')->toArray();
        if (empty($countryIds)) {
            return false;
        }

        $now = Carbon::now();
        $fullDate = $now->format('Y-m-d');
        $monthDay = $now->format('-m-d'); // e.g. -12-25

        $holiday = \App\Models\Holiday::whereIn('country_id', $countryIds)
            ->where('active', 1)
            ->where(function($query) use ($fullDate, $monthDay) {
                $query->where('holiday_date', $fullDate)
                      ->orWhere(function($q) use ($monthDay) {
                          $q->where('is_annual', 1)
                            ->where('holiday_date', 'like', '%' . $monthDay);
                      });
            })
            ->first();

        return $holiday ? true : false;
    }
}
