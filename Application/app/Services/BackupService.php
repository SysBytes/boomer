<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BackupService
{
    /**
     * Directorio base del proyecto (raíz, padre de Application/).
     */
    protected string $projectRoot;

    /**
     * Directorio donde se almacenan los respaldos.
     */
    protected string $backupBasePath;

    /**
     * Directorios y archivos a excluir del respaldo de archivos.
     */
    protected array $excludeDirs = [
        'Application/vendor',
        'Application/node_modules',
        'Application/storage/app/backups',
    ];

    public function __construct()
    {
        $this->projectRoot = realpath(base_path('..'));
        $this->backupBasePath = storage_path('app/backups');

        // Asegurar que existan los directorios de respaldo
        if (!File::isDirectory($this->backupBasePath . '/files')) {
            File::makeDirectory($this->backupBasePath . '/files', 0755, true);
        }
        if (!File::isDirectory($this->backupBasePath . '/database')) {
            File::makeDirectory($this->backupBasePath . '/database', 0755, true);
        }
    }

    /**
     * Crear respaldo de archivos del proyecto.
     * Incluye todos los directorios raíz excepto vendor/ y node_modules/.
     * Incluye archivos ocultos como .env.
     *
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    public function createFilesBackup(): array
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "archivos_{$timestamp}.zip";
        $filepath = $this->backupBasePath . '/files/' . $filename;

        try {
            $zip = new ZipArchive();

            if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return [
                    'success' => false,
                    'filename' => $filename,
                    'message' => 'No se pudo crear el archivo ZIP.',
                ];
            }

            $this->addDirectoryToZip($zip, $this->projectRoot, '');

            $zip->close();

            return [
                'success' => true,
                'filename' => $filename,
                'message' => 'Respaldo de archivos creado exitosamente.',
            ];
        } catch (\Exception $e) {
            // Limpiar archivo parcial si existe
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            Log::error('Error al crear respaldo de archivos: ' . $e->getMessage());

            return [
                'success' => false,
                'filename' => $filename,
                'message' => 'Error al crear el respaldo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Agregar un directorio recursivamente al ZIP.
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $realPath, string $relativePath): void
    {
        $items = scandir($realPath);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $realPath . DIRECTORY_SEPARATOR . $item;
            $entryName = $relativePath === '' ? $item : $relativePath . '/' . $item;

            // Verificar si debe excluirse
            if ($this->shouldExclude($entryName)) {
                continue;
            }

            if (is_dir($fullPath)) {
                $zip->addEmptyDir($entryName);
                $this->addDirectoryToZip($zip, $fullPath, $entryName);
            } elseif (is_file($fullPath) && is_readable($fullPath)) {
                $zip->addFile($fullPath, $entryName);
            }
        }
    }

    /**
     * Verificar si un path debe excluirse del respaldo.
     */
    protected function shouldExclude(string $relativePath): bool
    {
        $normalized = str_replace('\\', '/', $relativePath);

        foreach ($this->excludeDirs as $exclude) {
            $excludeNormalized = str_replace('\\', '/', $exclude);
            if ($normalized === $excludeNormalized || str_starts_with($normalized, $excludeNormalized . '/')) {
                return true;
            }
        }

        // Excluir archivos .DS_Store
        if (basename($normalized) === '.DS_Store') {
            return true;
        }

        // Excluir archivos y directorios de entorno de trabajo de Ungravity
        if (basename($normalized) === '.vscode' || str_starts_with($normalized, '.vscode/') || str_ends_with($normalized, '.code-workspace')) {
            return true;
        }

        return false;
    }

    /**
     * Crear respaldo de la base de datos MySQL.
     *
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    public function createDatabaseBackup(): array
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "basedatos_{$timestamp}.sql.gz";
        $filepath = $this->backupBasePath . '/database/' . $filename;

        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $mysqldump = env('MYSQLDUMP_COMMAND', 'mysqldump');

        // Construir el comando mysqldump
        $command = sprintf(
            'export PATH=$PATH:/usr/local/mysql/bin:/usr/local/bin:/opt/homebrew/bin:/usr/bin:/bin; %s --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers --add-drop-table %s | gzip > %s 2>&1',
            $mysqldump,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        try {
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                // Limpiar archivo parcial
                if (File::exists($filepath)) {
                    File::delete($filepath);
                }

                $errorMessage = implode("\n", $output);
                Log::error("Error en mysqldump (código {$returnVar}): {$errorMessage}");

                return [
                    'success' => false,
                    'filename' => $filename,
                    'message' => "Error al ejecutar mysqldump. Código: {$returnVar}",
                ];
            }

            // Verificar que el archivo se creó y no está vacío
            if (!File::exists($filepath) || File::size($filepath) === 0) {
                if (File::exists($filepath)) {
                    File::delete($filepath);
                }

                return [
                    'success' => false,
                    'filename' => $filename,
                    'message' => 'El archivo de respaldo está vacío o no se creó.',
                ];
            }

            return [
                'success' => true,
                'filename' => $filename,
                'message' => 'Respaldo de base de datos creado exitosamente.',
            ];
        } catch (\Exception $e) {
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            Log::error('Error al crear respaldo de BD: ' . $e->getMessage());

            return [
                'success' => false,
                'filename' => $filename,
                'message' => 'Error al crear el respaldo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Listar respaldos existentes por tipo.
     *
     * @param string $type 'files' o 'database'
     * @return array Lista de respaldos con nombre, tamaño y fecha
     */
    public function listBackups(string $type): array
    {
        $directory = $this->backupBasePath . '/' . $type;

        if (!File::isDirectory($directory)) {
            return [];
        }

        $pattern = $type === 'files' ? '*.zip' : '*.sql.gz';
        $files = glob($directory . '/' . $pattern);

        $backups = [];
        foreach ($files as $file) {
            $filename = basename($file);

            // Extraer fecha del nombre del archivo
            $date = $this->extractDateFromFilename($filename);

            $backups[] = [
                'filename' => $filename,
                'size' => $this->formatFileSize(filesize($file)),
                'size_bytes' => filesize($file),
                'date' => $date ? $date->format('d/m/Y H:i:s') : 'Desconocida',
                'date_carbon' => $date,
            ];
        }

        // Ordenar por fecha más reciente primero
        usort($backups, function ($a, $b) {
            if (!$a['date_carbon'] || !$b['date_carbon']) return 0;
            return $b['date_carbon']->timestamp - $a['date_carbon']->timestamp;
        });

        // Remover date_carbon del resultado (solo uso interno)
        return array_map(function ($backup) {
            unset($backup['date_carbon']);
            return $backup;
        }, $backups);
    }

    /**
     * Obtener el contenido (lista de archivos) de un respaldo ZIP.
     *
     * @param string $filename Nombre del archivo ZIP
     * @return array ['success' => bool, 'files' => array, 'message' => string]
     */
    public function getFileBackupContents(string $filename): array
    {
        $filepath = $this->backupBasePath . '/files/' . $filename;

        if (!File::exists($filepath)) {
            return [
                'success' => false,
                'files' => [],
                'message' => 'El archivo de respaldo no existe.',
            ];
        }

        try {
            $zip = new ZipArchive();

            if ($zip->open($filepath) !== true) {
                return [
                    'success' => false,
                    'files' => [],
                    'message' => 'No se pudo abrir el archivo ZIP.',
                ];
            }

            $files = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'];

                // No incluir directorios vacíos como entradas
                if (substr($name, -1) === '/') {
                    continue;
                }

                // Ocultar archivos de entorno de trabajo
                if (str_starts_with($name, '.vscode/') || str_ends_with($name, '.code-workspace')) {
                    continue;
                }

                $files[] = [
                    'index' => $i,
                    'name' => $name,
                    'size' => $this->formatFileSize($stat['size']),
                    'size_bytes' => $stat['size'],
                ];
            }

            $zip->close();

            // Ordenar por nombre
            usort($files, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return [
                'success' => true,
                'files' => $files,
                'total' => count($files),
                'message' => 'Contenido leído exitosamente.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'files' => [],
                'message' => 'Error al leer el archivo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Restaurar archivos seleccionados (o todos) desde un respaldo ZIP.
     *
     * @param string $filename Nombre del ZIP
     * @param array|null $selectedFiles Archivos a restaurar (null = todos)
     * @return array ['success' => bool, 'restored' => int, 'message' => string]
     */
    public function restoreFiles(string $filename, ?array $selectedFiles = null): array
    {
        $filepath = $this->backupBasePath . '/files/' . $filename;

        if (!File::exists($filepath)) {
            return [
                'success' => false,
                'restored' => 0,
                'message' => 'El archivo de respaldo no existe.',
            ];
        }

        try {
            $zip = new ZipArchive();

            if ($zip->open($filepath) !== true) {
                return [
                    'success' => false,
                    'restored' => 0,
                    'message' => 'No se pudo abrir el archivo ZIP.',
                ];
            }

            $restoredCount = 0;
            $destination = $this->projectRoot;

            if ($selectedFiles === null || empty($selectedFiles)) {
                // Restaurar todo
                $zip->extractTo($destination);
                $restoredCount = $zip->numFiles;
            } else {
                // Restaurar solo los archivos seleccionados
                foreach ($selectedFiles as $file) {
                    $file = trim($file);
                    if ($zip->locateName($file) !== false) {
                        $zip->extractTo($destination, [$file]);
                        $restoredCount++;
                    }
                }
            }

            $zip->close();

            return [
                'success' => true,
                'restored' => $restoredCount,
                'message' => "Se restauraron {$restoredCount} archivo(s) exitosamente.",
            ];
        } catch (\Exception $e) {
            Log::error('Error al restaurar archivos: ' . $e->getMessage());

            return [
                'success' => false,
                'restored' => 0,
                'message' => 'Error al restaurar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Restaurar la base de datos desde un respaldo .sql.gz.
     *
     * @param string $filename Nombre del archivo .sql.gz
     * @return array ['success' => bool, 'message' => string]
     */
    public function restoreDatabase(string $filename): array
    {
        $filepath = $this->backupBasePath . '/database/' . $filename;

        if (!File::exists($filepath)) {
            return [
                'success' => false,
                'message' => 'El archivo de respaldo no existe.',
            ];
        }

        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $mysql = env('MYSQL_COMMAND', 'mysql');

        $command = sprintf(
            'export PATH=$PATH:/usr/local/mysql/bin:/usr/local/bin:/opt/homebrew/bin:/usr/bin:/bin; gunzip < %s | %s --host=%s --port=%s --user=%s --password=%s %s 2>&1',
            escapeshellarg($filepath),
            $mysql,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database)
        );

        try {
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMessage = implode("\n", $output);
                Log::error("Error al restaurar BD (código {$returnVar}): {$errorMessage}");

                return [
                    'success' => false,
                    'message' => "Error al restaurar la base de datos. Código: {$returnVar}",
                ];
            }

            return [
                'success' => true,
                'message' => 'Base de datos restaurada exitosamente.',
            ];
        } catch (\Exception $e) {
            Log::error('Error al restaurar BD: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Eliminar un archivo de respaldo.
     *
     * @param string $type 'files' o 'database'
     * @param string $filename Nombre del archivo
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteBackup(string $type, string $filename): array
    {
        // Sanitizar filename para evitar path traversal
        $filename = basename($filename);
        $filepath = $this->backupBasePath . '/' . $type . '/' . $filename;

        if (!File::exists($filepath)) {
            return [
                'success' => false,
                'message' => 'El archivo de respaldo no existe.',
            ];
        }

        try {
            File::delete($filepath);

            return [
                'success' => true,
                'message' => 'Respaldo eliminado exitosamente.',
            ];
        } catch (\Exception $e) {
            Log::error('Error al eliminar respaldo: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al eliminar el respaldo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Limpiar respaldos antiguos según la política de retención.
     *
     * @param string $type 'files' o 'database'
     * @param int $retentionDays Días a conservar
     * @return int Cantidad de respaldos eliminados
     */
    public function cleanOldBackups(string $type, int $retentionDays): int
    {
        if ($retentionDays <= 0) {
            return 0;
        }

        $directory = $this->backupBasePath . '/' . $type;
        $pattern = $type === 'files' ? '*.zip' : '*.sql.gz';
        $files = glob($directory . '/' . $pattern);
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        $deleted = 0;

        foreach ($files as $file) {
            $date = $this->extractDateFromFilename(basename($file));
            if ($date && $date->lt($cutoffDate)) {
                File::delete($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Extraer fecha del nombre del archivo de respaldo.
     * Formato esperado: tipo_YYYY-MM-DD_HH-mm-ss.ext
     */
    protected function extractDateFromFilename(string $filename): ?Carbon
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $filename, $matches)) {
            try {
                return Carbon::createFromFormat('Y-m-d_H-i-s', $matches[1]);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Formatear tamaño de archivo legible.
     */
    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
