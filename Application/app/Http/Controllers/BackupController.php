<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BackupService;
use App\Models\BackupSetting;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct()
    {
        $this->backupService = new BackupService();

        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    /**
     * Vista principal con listado de respaldos (tabs Archivos / Base de Datos).
     */
    public function index()
    {
        $fileBackups = $this->backupService->listBackups('files');
        $dbBackups = $this->backupService->listBackups('database');

        return view('backoffice.backup.index', compact('fileBackups', 'dbBackups'));
    }

    /**
     * Crear respaldo de archivos.
     */
    public function createFiles()
    {
        $result = $this->backupService->createFilesBackup();

        return redirect()->route('backups.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Crear respaldo de base de datos.
     */
    public function createDatabase()
    {
        $result = $this->backupService->createDatabaseBackup();

        return redirect()->route('backups.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Ver contenido de un respaldo de archivos (AJAX).
     */
    public function showContents(string $filename)
    {
        $filename = basename($filename);
        $result = $this->backupService->getFileBackupContents($filename);

        return response()->json($result);
    }

    /**
     * Restaurar archivos seleccionados desde un respaldo.
     */
    public function restoreFiles(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'selected_files' => 'nullable|array',
            'selected_files.*' => 'string',
            'restore_all' => 'nullable|boolean',
        ]);

        $filename = basename($request->filename);
        $selectedFiles = $request->boolean('restore_all') ? null : $request->selected_files;

        $result = $this->backupService->restoreFiles($filename, $selectedFiles);

        return redirect()->route('backups.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Restaurar base de datos desde un respaldo.
     */
    public function restoreDatabase(string $filename)
    {
        $filename = basename($filename);
        $result = $this->backupService->restoreDatabase($filename);

        return redirect()->route('backups.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Descargar un respaldo.
     */
    public function download(string $type, string $filename)
    {
        if (!in_array($type, ['files', 'database'])) {
            abort(404);
        }

        $filename = basename($filename);
        $path = storage_path("app/backups/{$type}/{$filename}");

        if (!\Illuminate\Support\Facades\File::exists($path)) {
            abort(404, 'El archivo de respaldo no existe.');
        }

        return response()->download($path);
    }

    /**
     * Eliminar un respaldo.
     */
    public function delete(string $type, string $filename)
    {
        if (!in_array($type, ['files', 'database'])) {
            return redirect()->route('backups.index')->with('error', 'Tipo de respaldo inválido.');
        }

        $filename = basename($filename);
        $result = $this->backupService->deleteBackup($type, $filename);

        return redirect()->route('backups.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Vista de configuración de respaldos programados.
     */
    public function settings()
    {
        $filesSetting = BackupSetting::forFiles();
        $dbSetting = BackupSetting::forDatabase();

        $frequencies = BackupSetting::frequencies();
        $daysOfWeek = BackupSetting::daysOfWeek();

        return view('backoffice.backup.settings', compact(
            'filesSetting',
            'dbSetting',
            'frequencies',
            'daysOfWeek'
        ));
    }

    /**
     * Actualizar configuración de respaldos programados.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'files_enabled' => 'nullable|boolean',
            'files_frequency' => 'required|in:daily,weekly,monthly',
            'files_time' => 'required|date_format:H:i',
            'files_day_of_week' => 'nullable|integer|min:0|max:6',
            'files_day_of_month' => 'nullable|integer|min:1|max:28',
            'files_retention_days' => 'required|integer|min:1|max:365',

            'database_enabled' => 'nullable|boolean',
            'database_frequency' => 'required|in:daily,weekly,monthly',
            'database_time' => 'required|date_format:H:i',
            'database_day_of_week' => 'nullable|integer|min:0|max:6',
            'database_day_of_month' => 'nullable|integer|min:1|max:28',
            'database_retention_days' => 'required|integer|min:1|max:365',
        ]);

        // Actualizar configuración de archivos
        BackupSetting::updateOrCreate(
            ['backup_type' => 'files'],
            [
                'enabled' => $request->boolean('files_enabled'),
                'frequency' => $request->files_frequency,
                'time' => $request->files_time,
                'day_of_week' => $request->files_day_of_week,
                'day_of_month' => $request->files_day_of_month,
                'retention_days' => $request->files_retention_days,
            ]
        );

        // Actualizar configuración de base de datos
        BackupSetting::updateOrCreate(
            ['backup_type' => 'database'],
            [
                'enabled' => $request->boolean('database_enabled'),
                'frequency' => $request->database_frequency,
                'time' => $request->database_time,
                'day_of_week' => $request->database_day_of_week,
                'day_of_month' => $request->database_day_of_month,
                'retention_days' => $request->database_retention_days,
            ]
        );

        return redirect()->route('backups.settings')
            ->with('success', 'Configuración de respaldos actualizada correctamente.');
    }
}
