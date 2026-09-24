<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Exception;
use PDO;

class DatabaseBackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups/db');
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Get directory path
     */
    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * List all database backups
     */
    public function getBackupsList(): array
    {
        if (!is_dir($this->backupDir)) {
            return [];
        }

        $files = scandir($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['sql', 'gz'], true)) {
                continue;
            }

            $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($filePath)) {
                continue;
            }

            $size = filesize($filePath);
            $mtime = filemtime($filePath);

            $backups[] = [
                'filename'       => $file,
                'path'           => $filePath,
                'size'           => $size,
                'size_formatted' => $this->formatBytes($size),
                'created_at'     => date('Y-m-d H:i:s', $mtime),
                'created_diff'   => date('d M Y, h:i A', $mtime),
                'timestamp'      => $mtime,
                'is_compressed'  => $ext === 'gz',
            ];
        }

        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Create full database backup (.sql or .sql.gz)
     */
    public function createBackup(bool $compress = false): array
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $dbName = config('database.connections.mysql.database');
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "db_backup_{$timestamp}." . ($compress ? 'sql.gz' : 'sql');
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch all base tables
        $tablesResult = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'");
        $tables = array_map(fn($t) => $t->TABLE_NAME, $tablesResult);

        $handle = $compress ? gzopen($filePath, 'wb9') : fopen($filePath, 'w');
        if (!$handle) {
            throw new Exception("Unable to create backup file at {$filePath}");
        }

        $write = function(string $data) use ($handle, $compress) {
            if ($compress) {
                gzwrite($handle, $data);
            } else {
                fwrite($handle, $data);
            }
        };

        // Write SQL Header
        $write("-- -------------------------------------------------------------\n");
        $write("-- Database Backup: {$dbName}\n");
        $write("-- Generated At: " . date('Y-m-d H:i:s') . "\n");
        $write("-- PHP Version: " . phpversion() . "\n");
        $write("-- -------------------------------------------------------------\n\n");
        $write("SET FOREIGN_KEY_CHECKS=0;\n");
        $write("SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        $write("SET time_zone=\"+00:00\";\n");
        $write("SET NAMES utf8mb4;\n\n");

        foreach ($tables as $table) {
            // Drop table
            $write("--\n-- Table structure for table `{$table}`\n--\n");
            $write("DROP TABLE IF EXISTS `{$table}`;\n");

            // Create table statement
            $createTableResult = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($createTableResult)) {
                $createSql = $createTableResult[0]->{'Create Table'} ?? null;
                if ($createSql) {
                    $write($createSql . ";\n\n");
                }
            }

            // Dump data using chunked cursor to keep memory usage low
            $write("--\n-- Dumping data for table `{$table}`\n--\n");
            $query = DB::table($table);
            $count = $query->count();

            if ($count > 0) {
                $write("LOCK TABLES `{$table}` WRITE;\n");
                $write("/*!40000 ALTER TABLE `{$table}` DISABLE KEYS */;\n");

                $chunkSize = 500;
                $batchRows = [];

                foreach ($query->cursor() as $row) {
                    $vals = [];
                    foreach ((array)$row as $val) {
                        if (is_null($val)) {
                            $vals[] = 'NULL';
                        } elseif (is_numeric($val) && !is_string($val)) {
                            $vals[] = $val;
                        } else {
                            $vals[] = $pdo->quote((string)$val);
                        }
                    }
                    $batchRows[] = '(' . implode(',', $vals) . ')';

                    if (count($batchRows) >= $chunkSize) {
                        $write("INSERT INTO `{$table}` VALUES " . implode(",\n", $batchRows) . ";\n");
                        $batchRows = [];
                    }
                }

                if (!empty($batchRows)) {
                    $write("INSERT INTO `{$table}` VALUES " . implode(",\n", $batchRows) . ";\n");
                }

                $write("/*!40000 ALTER TABLE `{$table}` ENABLE KEYS */;\n");
                $write("UNLOCK TABLES;\n\n");
            }
        }

        // Footer
        $write("SET FOREIGN_KEY_CHECKS=1;\n");
        $write("-- -------------------------------------------------------------\n");
        $write("-- Backup Completed Successfully\n");
        $write("-- -------------------------------------------------------------\n");

        if ($compress) {
            gzclose($handle);
        } else {
            fclose($handle);
        }

        $fileSize = filesize($filePath);

        return [
            'success'        => true,
            'filename'       => $filename,
            'path'           => $filePath,
            'size'           => $fileSize,
            'size_formatted' => $this->formatBytes($fileSize),
            'tables_count'   => count($tables),
        ];
    }

    /**
     * Restore database from .sql or .sql.gz file
     */
    public function restoreBackup(string $filePath): array
    {
        @set_time_limit(900);
        @ini_set('memory_limit', '512M');

        if (!file_exists($filePath)) {
            throw new Exception("Backup file not found at: {$filePath}");
        }

        $isCompressed = str_ends_with(strtolower($filePath), '.gz');
        $handle = $isCompressed ? gzopen($filePath, 'rb') : fopen($filePath, 'r');

        if (!$handle) {
            throw new Exception("Unable to open backup file for reading.");
        }

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Turn off checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        $pdo->exec("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");

        $statement = '';
        $executedStatements = 0;

        while (!($isCompressed ? gzeof($handle) : feof($handle))) {
            $line = $isCompressed ? gzgets($handle, 65536) : fgets($handle, 65536);
            if ($line === false) {
                break;
            }

            $trimmed = trim($line);

            // Skip comments and empty lines
            if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '#')) {
                continue;
            }

            $statement .= $line;

            // Check if statement ends with semicolon
            if (str_ends_with($trimmed, ';')) {
                try {
                    $pdo->exec($statement);
                    $executedStatements++;
                } catch (Exception $e) {
                    // Log or handle non-fatal warnings
                }
                $statement = '';
            }
        }

        if (trim($statement) !== '') {
            try {
                $pdo->exec($statement);
                $executedStatements++;
            } catch (Exception $e) {}
        }

        if ($isCompressed) {
            gzclose($handle);
        } else {
            fclose($handle);
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

        // Clear application caches
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
        } catch (Exception $e) {}

        return [
            'success'             => true,
            'executed_statements' => $executedStatements,
        ];
    }

    /**
     * Delete backup file
     */
    public function deleteBackup(string $filename): bool
    {
        $filename = basename($filename);
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($path) && is_file($path)) {
            return @unlink($path);
        }

        return false;
    }

    /**
     * Format bytes to human readable string
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int)floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
