<?php

namespace App\Console\Commands;

use App\Mail\PendingOvertimesMail;
use App\Models\Overtime;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyPendingOvertimes extends Command
{
    protected $signature = 'overtimes:notify-pending';
    protected $description = 'Envía correos de notificación de horas extras pendientes a supervisores y administradores';

    public function handle()
    {
        $this->info('Iniciando notificación de horas extras pendientes...');

        $pendingOvertimes = Overtime::where('status', 'pending')
            ->with(['user.roles', 'work.supervisor', 'work.client'])
            ->get();

        if ($pendingOvertimes->isEmpty()) {
            $this->info('No hay horas extras pendientes.');
            return 0;
        }

        // ───────────────────────────────────────
        // 1. Horas de technician/assistant → Supervisor del proyecto
        // ───────────────────────────────────────
        $techOvertimes = $pendingOvertimes->filter(function ($ot) {
            return $ot->user && $ot->user->hasAnyRole(['technician', 'assistant']);
        });

        // Agrupar por supervisor del proyecto (work.supervisor_id)
        $bySupervisor = $techOvertimes->groupBy(function ($ot) {
            return $ot->work->supervisor_id ?? 0;
        });

        foreach ($bySupervisor as $supervisorId => $overtimes) {
            if (!$supervisorId) {
                continue;
            }

            $supervisor = User::find($supervisorId);
            if (!$supervisor || !$supervisor->email) {
                $this->warn("Supervisor ID {$supervisorId} no encontrado o sin email.");
                continue;
            }

            Mail::to($supervisor->email)->send(
                new PendingOvertimesMail(
                    recipientName: $supervisor->name,
                    overtimes: $overtimes,
                    recipientType: 'supervisor'
                )
            );

            $this->info("Correo enviado al supervisor: {$supervisor->name} ({$supervisor->email}) — {$overtimes->count()} horas extra pendientes.");
        }

        // ───────────────────────────────────────
        // 2. Horas de otros roles → Admins
        // ───────────────────────────────────────
        $otherOvertimes = $pendingOvertimes->filter(function ($ot) {
            return $ot->user && !$ot->user->hasAnyRole(['technician', 'assistant']);
        });

        if ($otherOvertimes->isNotEmpty()) {
            $admins = User::whereHas('roles', function ($q) {
                $q->where('slug', 'admin');
            })->get();

            foreach ($admins as $admin) {
                if (!$admin->email) {
                    continue;
                }

                Mail::to($admin->email)->send(
                    new PendingOvertimesMail(
                        recipientName: $admin->name,
                        overtimes: $otherOvertimes,
                        recipientType: 'admin'
                    )
                );

                $this->info("Correo enviado al admin: {$admin->name} ({$admin->email}) — {$otherOvertimes->count()} horas extra pendientes.");
            }
        }

        // ───────────────────────────────────────
        // 3. Todas las horas pendientes → Developers
        // ───────────────────────────────────────
        if ($pendingOvertimes->isNotEmpty()) {
            $developers = User::whereHas('roles', function ($q) {
                $q->where('slug', 'developer');
            })->get();

            foreach ($developers as $developer) {
                if (!$developer->email) {
                    continue;
                }

                Mail::to($developer->email)->send(
                    new PendingOvertimesMail(
                        recipientName: $developer->name,
                        overtimes: $pendingOvertimes,
                        recipientType: 'developer'
                    )
                );

                $this->info("Correo enviado al developer: {$developer->name} ({$developer->email}) — {$pendingOvertimes->count()} horas extra pendientes totales.");
            }
        }

        $this->info('Proceso de notificación completado.');
        return 0;
    }
}
