<?php

namespace App\Services;

use App\Models\Setting;
use RuntimeException;
use Symfony\Component\Process\Process;

class BackupService
{
    public function directoryPath(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('local')->path('backups');
    }

    public function settings(): array
    {
        return [
            'frequency' => Setting::get('backup', 'frequency', 'off'),
            'time' => Setting::get('backup', 'time', '02:00'),
            'keep' => Setting::get('backup', 'keep', '10'),
        ];
    }

    public function keep(): int
    {
        return max(1, (int) Setting::get('backup', 'keep', '10'));
    }

    public function create(string $reason = 'manual'): array
    {
        $binary = $this->findMysqldumpBinary();

        if (!$binary) {
            throw new RuntimeException('mysqldump binary not found. Set MYSQLDUMP_PATH in your .env file.');
        }

        $this->ensureDirectory();

        $type = in_array($reason, ['auto', 'manual', 'prerestore'], true) ? $reason : 'manual';
        $filename = sprintf('backup_%s_%s.sql', now()->format('Y-m-d_His'), $type);
        $path = $this->path($filename);

        $connection = config('database.default');
        $db = config('database.connections.' . $connection);

        $args = [
            $binary,
            '--host=' . $db['host'],
            '--port=' . (string) ($db['port'] ?? '3306'),
            '--user=' . $db['username'],
            '--routines',
            '--triggers',
            '--single-transaction',
            '--opt',
            '--default-character-set=utf8mb4',
            $db['database'],
        ];

        $process = new Process($args);

        if (!empty($db['password'])) {
            $process->setEnv(array_merge($process->getEnv(), ['MYSQL_PWD' => $db['password']]));
        }

        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Database backup failed: ' . $process->getErrorOutput());
        }

        file_put_contents($path, $process->getOutput());

        $this->prune($this->keep());

        return [
            'filename' => $filename,
            'path' => $path,
            'size' => filesize($path) ?: 0,
            'size_human' => $this->humanSize(filesize($path) ?: 0),
        ];
    }

    public function list(): array
    {
        $this->ensureDirectory();

        $files = glob($this->directoryPath() . DIRECTORY_SEPARATOR . 'backup_*.sql') ?: [];
        rsort($files);

        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);

            if (!preg_match('/^backup_(\d{4}-\d{2}-\d{2})_(\d{6})_(auto|manual|prerestore)\.sql$/', $filename, $m)) {
                continue;
            }

            $size = filesize($file) ?: 0;
            $type = $m[3] === 'auto' ? 'Auto' : ($m[3] === 'prerestore' ? 'Pre-restore' : 'Manual');

            $backups[] = [
                'filename' => $filename,
                'path' => $file,
                'size' => $size,
                'size_human' => $this->humanSize($size),
                'created_at' => \Illuminate\Support\Carbon::parse(
                    $m[1] . ' ' . substr($m[2], 0, 2) . ':' . substr($m[2], 2, 2) . ':' . substr($m[2], 4, 2)
                ),
                'type' => $type,
            ];
        }

        return $backups;
    }

    public function file(string $filename): array
    {
        $filename = basename($filename);

        if (!preg_match('/^backup_.+\.sql$/', $filename)) {
            throw new RuntimeException('Invalid backup filename.');
        }

        $path = $this->path($filename);

        if (!is_file($path)) {
            throw new RuntimeException('Backup not found.');
        }

        $size = filesize($path) ?: 0;

        return [
            'filename' => $filename,
            'path' => $path,
            'size' => $size,
            'size_human' => $this->humanSize($size),
        ];
    }

    public function delete(string $filename): bool
    {
        $filename = basename($filename);

        if (!preg_match('/^backup_.+\.sql$/', $filename)) {
            return false;
        }

        $path = $this->path($filename);

        return is_file($path) && @unlink($path);
    }

    public function restore(string $filename): array
    {
        $file = $this->file($filename);

        $binary = $this->findMysqlClient();

        if (!$binary) {
            throw new RuntimeException('mysql client not found. Set MYSQL_PATH in your .env file.');
        }

        $connection = config('database.default');
        $db = config('database.connections.' . $connection);

        $args = [
            $binary,
            '--host=' . $db['host'],
            '--port=' . (string) ($db['port'] ?? '3306'),
            '--user=' . $db['username'],
            '--default-character-set=utf8mb4',
            $db['database'],
        ];

        $process = new Process($args);

        if (!empty($db['password'])) {
            $process->setEnv(array_merge($process->getEnv(), ['MYSQL_PWD' => $db['password']]));
        }

        $process->setInput((string) file_get_contents($file['path']));
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Database restore failed: ' . $process->getErrorOutput());
        }

        return $file;
    }

    public function prune(int $keep): int
    {
        $autos = array_values(array_filter(
            $this->list(),
            fn ($backup) => $backup['type'] === 'Auto'
        ));

        $removed = 0;

        foreach (array_slice($autos, $keep) as $old) {
            if (@unlink($old['path'])) {
                $removed++;
            }
        }

        return $removed;
    }

    protected function path(string $filename): string
    {
        return $this->directoryPath() . DIRECTORY_SEPARATOR . $filename;
    }

    protected function ensureDirectory(): void
    {
        if (!is_dir($this->directoryPath())) {
            mkdir($this->directoryPath(), 0777, true);
        }
    }

    protected function findMysqldumpBinary(): ?string
    {
        return $this->findBinaryFor(['mysqldump']);
    }

    protected function findMysqlClient(): ?string
    {
        return $this->findBinaryFor(['mysql']);
    }

    protected function findBinaryFor(array $names): ?string
    {
        $executables = array_map(fn ($name) => $name . '.exe', $names);

        $candidates = [];

        if ($names === ['mysqldump'] && env('MYSQLDUMP_PATH')) {
            $candidates[] = env('MYSQLDUMP_PATH');
        }

        if ($names === ['mysql'] && env('MYSQL_PATH')) {
            $candidates[] = env('MYSQL_PATH');
        }

        $patterns = [
            'D:\wamp\bin\mysql\mysql*\bin',
            'C:\wamp\bin\mysql\mysql*\bin',
            'E:\wamp\bin\mysql\mysql*\bin',
        ];

        foreach ($patterns as $pattern) {
            foreach ($names as $name) {
                foreach ((array) glob($pattern . DIRECTORY_SEPARATOR . $name . '.exe') as $candidate) {
                    $candidates[] = $candidate;
                }
            }
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && is_file($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        foreach ($names as $name) {
            if ($this->commandExists($name)) {
                return $name;
            }
        }

        return null;
    }

    protected function commandExists(string $command): bool
    {
        $result = null;
        @exec('where ' . $command . ' 2>NUL', $result);

        return is_array($result) && count(array_filter($result)) > 0;
    }

    protected function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $i ? 1 : 0) . ' ' . $units[$i];
    }
}