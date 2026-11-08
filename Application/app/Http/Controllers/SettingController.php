<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Work;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $weeklyWorkHours = $this->getEnv('WEEKLY_WORK_HOURS', '44');
        $defaultStaffProject = $this->getEnv('DEFAULT_STAFF_PROJECT', '');
        $whatsappEnabled = $this->getEnv('WHATSAPP_NOTIFICATIONS_ENABLED', 'true');
        $notificationMethod = $this->getEnv('NOTIFICATION_METHOD', 'whatsapp');
        $works = Work::where('active', 1)->with('location.client')->get();
        return view('backoffice.setting.index', compact('weeklyWorkHours', 'defaultStaffProject', 'whatsappEnabled', 'notificationMethod', 'works'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'WEEKLY_WORK_HOURS' => 'required|numeric|min:1|max:168',
            'DEFAULT_STAFF_PROJECT' => 'nullable|string',
            'NOTIFICATION_METHOD' => 'required|in:whatsapp,sms',
        ]);

        $this->setEnv('WEEKLY_WORK_HOURS', $request->WEEKLY_WORK_HOURS);
        if ($request->has('DEFAULT_STAFF_PROJECT')) {
            $this->setEnv('DEFAULT_STAFF_PROJECT', $request->DEFAULT_STAFF_PROJECT ?? '');
        }

        $whatsappEnabled = $request->has('WHATSAPP_NOTIFICATIONS_ENABLED') ? 'true' : 'false';
        $this->setEnv('WHATSAPP_NOTIFICATIONS_ENABLED', $whatsappEnabled);
        
        $this->setEnv('NOTIFICATION_METHOD', $request->NOTIFICATION_METHOD);

        return redirect()->route('setting.index')->with('success', 'Configuración actualizada correctamente.');
    }

    private function getEnv($key, $default = '')
    {
        $path = base_path('.env');
        if (File::exists($path)) {
            $content = File::get($path);
            if (preg_match("/^{$key}=(.*)$/m", $content, $matches)) {
                return trim($matches[1]);
            }
        }
        return env($key, $default);
    }

    private function setEnv($key, $value)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            $content = File::get($path);
            
            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}\n";
            }

            file_put_contents($path, $content);
            Artisan::call('config:clear');
        }
    }
}
