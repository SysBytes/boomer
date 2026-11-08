<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Throwable;

class DeveloperController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->hasRole('developer')) {
                abort(403, 'Acceso exclusivo para desarrolladores.');
            }
            return $next($request);
        });
    }

    /**
     * Muestra el panel de herramientas para desarrolladores.
     */
    public function index()
    {
        return view('backoffice.developer.index');
    }

    /**
     * Ejecuta un comando Artisan permitido y devuelve la salida en JSON.
     */
    public function execute(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
        ]);

        $action = $request->input('action');

        // Lista blanca estricta de comandos permitidos
        $allowedCommands = [
            'optimize:clear' => [
                'command' => 'optimize:clear',
                'params' => [],
                'label' => 'php artisan optimize:clear',
            ],
            'route:cache' => [
                'command' => 'route:cache',
                'params' => [],
                'label' => 'php artisan route:cache',
            ],
            'view:cache' => [
                'command' => 'view:cache',
                'params' => [],
                'label' => 'php artisan view:cache',
            ],
            'config:cache' => [
                'command' => 'config:cache',
                'params' => [],
                'label' => 'php artisan config:cache',
            ],
            'storage:link' => [
                'command' => 'storage:link',
                'params' => [],
                'label' => 'php artisan storage:link',
            ],
            'migrate' => [
                'command' => 'migrate',
                'params' => ['--force' => true],
                'label' => 'php artisan migrate --force',
            ],
            'migrate:status' => [
                'command' => 'migrate:status',
                'params' => [],
                'label' => 'php artisan migrate:status',
            ],
            'check-missing-checkouts' => [
                'command' => 'app:check-missing-checkouts',
                'params' => [],
                'label' => 'php artisan app:check-missing-checkouts',
            ],
        ];

        if (!array_key_exists($action, $allowedCommands)) {
            return response()->json([
                'success' => false,
                'command' => $action,
                'output' => "Error: El comando solicitado '{$action}' no está permitido.",
            ], 422);
        }

        $cmdConfig = $allowedCommands[$action];
        $timestamp = now()->format('Y-m-d H:i:s');

        try {
            $exitCode = Artisan::call($cmdConfig['command'], $cmdConfig['params']);
            $output = trim(Artisan::output());

            if ($output === '') {
                $output = 'Comando ejecutado correctamente (sin salida adicional).';
            }

            return response()->json([
                'success' => $exitCode === 0,
                'command' => $cmdConfig['label'],
                'timestamp' => $timestamp,
                'exit_code' => $exitCode,
                'output' => $output,
            ]);
        } catch (Throwable $e) {
            if ($action === 'route:cache') {
                Artisan::call('route:clear');
            }

            return response()->json([
                'success' => false,
                'command' => $cmdConfig['label'],
                'timestamp' => $timestamp,
                'exit_code' => 1,
                'output' => "EXCEPCIÓN AL EJECUTAR EL COMANDO:\n"
                    . $e->getMessage() . "\n\n"
                    . "Archivo: " . $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * Visualiza los errores 500 y registros del archivo de logs de Laravel.
     */
    public function logs(Request $request)
    {
        $logDir = storage_path('logs');
        $availableFiles = [];

        if (File::isDirectory($logDir)) {
            $files = File::glob($logDir . '/*.log');
            rsort($files);
            foreach ($files as $filePath) {
                $availableFiles[] = basename($filePath);
            }
        }

        $selectedFile = $request->query('file', $availableFiles[0] ?? 'laravel.log');
        // Evitar Path Traversal
        $selectedFile = basename($selectedFile);
        $levelFilter = $request->query('level', 'errors'); // 'errors' (solo 500/errores) o 'all'

        $filePath = $logDir . DIRECTORY_SEPARATOR . $selectedFile;
        $entries = [];
        $fileSizeFormatted = '0 KB';

        if (File::exists($filePath) && File::isFile($filePath)) {
            $sizeBytes = File::size($filePath);
            $fileSizeFormatted = $this->formatBytes($sizeBytes);

            // Leer máximo los últimos 2 MB para no agotar memoria en hosting compartido
            $maxBytes = 2 * 1024 * 1024;
            $content = $this->tailFileContent($filePath, $maxBytes);
            $entries = $this->parseLogEntries($content, $levelFilter);
        }

        return view('backoffice.developer.logs', compact(
            'availableFiles',
            'selectedFile',
            'levelFilter',
            'entries',
            'fileSizeFormatted'
        ));
    }

    /**
     * Limpia (vacía) el archivo de log seleccionado.
     */
    public function clearLogs(Request $request)
    {
        $selectedFile = basename($request->input('file', 'laravel.log'));
        $filePath = storage_path('logs' . DIRECTORY_SEPARATOR . $selectedFile);

        if (File::exists($filePath) && File::isFile($filePath) && str_ends_with($selectedFile, '.log')) {
            File::put($filePath, '');
            return redirect()->route('developer.logs', ['file' => $selectedFile])
                ->with('success', "El archivo de registro '{$selectedFile}' ha sido vaciado correctamente.");
        }

        return redirect()->route('developer.logs')
            ->with('error', 'No se encontró el archivo de log especificado.');
    }

    /**
     * Descarga el archivo de log completo.
     */
    public function downloadLog(Request $request)
    {
        $selectedFile = basename($request->query('file', 'laravel.log'));
        $filePath = storage_path('logs' . DIRECTORY_SEPARATOR . $selectedFile);

        if (!File::exists($filePath) || !File::isFile($filePath) || !str_ends_with($selectedFile, '.log')) {
            abort(404, 'Archivo de log no encontrado.');
        }

        return response()->download($filePath, $selectedFile);
    }

    /**
     * Lee los últimos N bytes de un archivo de forma eficiente.
     */
    private function tailFileContent(string $filePath, int $maxBytes): string
    {
        $size = filesize($filePath);
        if ($size === false || $size === 0) {
            return '';
        }

        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return '';
        }

        if ($size > $maxBytes) {
            fseek($handle, -$maxBytes, SEEK_END);
            // Descartar primera línea parcial si empezamos a mitad de archivo
            fgets($handle);
        }

        $content = stream_get_contents($handle);
        fclose($handle);

        return $content ?: '';
    }

    /**
     * Parsea las entradas de Monolog/Laravel en un arreglo estructurado (más recientes primero).
     */
    private function parseLogEntries(string $content, string $levelFilter = 'errors'): array
    {
        if (trim($content) === '') {
            return [];
        }

        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:\d{2})?)\]\s+(?:(\w+)\.)?([A-Z]+):\s+(.*)/m';
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return [];
        }

        $errorLevels = ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];
        $entries = [];
        $count = count($matches[0]);

        for ($i = 0; $i < $count; $i++) {
            $level = strtoupper($matches[3][$i][0]);

            if ($levelFilter === 'errors' && !in_array($level, $errorLevels, true)) {
                continue;
            }

            $startPos = $matches[0][$i][1];
            $headerLen = strlen($matches[0][$i][0]);
            $bodyStart = $startPos + $headerLen;
            $endPos = ($i + 1 < $count) ? $matches[0][$i + 1][1] : strlen($content);

            $firstLineMessage = trim($matches[4][$i][0]);
            $restOfEntry = trim(substr($content, $bodyStart, $endPos - $bodyStart));

            // Separar el mensaje principal del stacktrace si existe
            $summary = $firstLineMessage;
            $location = null;

            // Extraer archivo y línea si aparece como " at /path/file.php:123" o "in /path/file.php:123"
            if (preg_match('/(?:at|in)\s+([^\s]+\.php(?:\(\d+\)|:\d+))/i', $firstLineMessage . ' ' . $restOfEntry, $locMatch)) {
                $location = str_replace(base_path() . '/', '', $locMatch[1]);
            }

            // Limpiar el resumen para la tabla si contiene JSON de excepción largo
            if (($jsonPos = strpos($summary, '{"exception":')) !== false) {
                $cleanSummary = trim(substr($summary, 0, $jsonPos));
                if ($cleanSummary !== '') {
                    $summary = $cleanSummary;
                }
            }

            $fullTrace = trim($firstLineMessage . ($restOfEntry !== '' ? "\n" . $restOfEntry : ''));

            $entries[] = [
                'timestamp' => $matches[1][$i][0],
                'env' => $matches[2][$i][0] ?: 'production',
                'level' => $level,
                'summary' => $summary,
                'location' => $location,
                'full_trace' => $fullTrace,
            ];
        }

        // Mostrar los más recientes primero y limitar a los últimos 200 registros para fluidez en el navegador
        return array_slice(array_reverse($entries), 0, 200);
    }

    /**
     * Formatea bytes a unidades legibles.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
