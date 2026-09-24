<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Media;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use Exception;

class MediaBackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups/images');
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
     * List all image / media zip backups
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
            if ($ext !== 'zip') {
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
            ];
        }

        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Get total size and count of current uploads
     */
    public function getCurrentUploadsStats(): array
    {
        $uploadsDir = public_path('uploads');
        if (!is_dir($uploadsDir)) {
            return ['count' => 0, 'size' => 0, 'size_formatted' => '0 B'];
        }

        $count = 0;
        $totalBytes = 0;

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($uploadsDir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $count++;
                    $totalBytes += $item->getSize();
                }
            }
        } catch (Exception $e) {}

        return [
            'count'          => $count,
            'size'           => $totalBytes,
            'size_formatted' => $this->formatBytes($totalBytes),
        ];
    }

    /**
     * Create zip backup of all uploaded images
     */
    public function createBackup(): array
    {
        @set_time_limit(900);
        @ini_set('memory_limit', '512M');

        if (!class_exists('ZipArchive')) {
            throw new Exception("PHP ZipArchive extension is not enabled on this server.");
        }

        $timestamp = date('Y-m-d_H-i-s');
        $filename = "images_backup_{$timestamp}.zip";
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $zip = new ZipArchive();
        $res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($res !== true) {
            throw new Exception("Could not create ZIP archive at: {$zipPath} (Error Code: {$res})");
        }

        $addedCount = 0;
        $uploadsDir = public_path('uploads');

        if (is_dir($uploadsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($uploadsDir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $realPath = $file->getRealPath();
                    $relative = substr($realPath, strlen($uploadsDir) + 1);
                    $relativeZipPath = 'uploads/' . str_replace('\\', '/', $relative);

                    $zip->addFile($realPath, $relativeZipPath);
                    $addedCount++;
                }
            }
        }

        // Also check if public/complaints has images
        $complaintsDir = public_path('complaints');
        if (is_dir($complaintsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($complaintsDir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $realPath = $file->getRealPath();
                    $relative = substr($realPath, strlen($complaintsDir) + 1);
                    $relativeZipPath = 'complaints/' . str_replace('\\', '/', $relative);

                    $zip->addFile($realPath, $relativeZipPath);
                    $addedCount++;
                }
            }
        }

        $zip->close();

        if (!file_exists($zipPath)) {
            throw new Exception("ZIP archive could not be saved.");
        }

        $fileSize = filesize($zipPath);

        return [
            'success'        => true,
            'filename'       => $filename,
            'path'           => $zipPath,
            'size'           => $fileSize,
            'size_formatted' => $this->formatBytes($fileSize),
            'files_count'    => $addedCount,
        ];
    }

    /**
     * Restore images from ZIP archive
     */
    public function restoreBackup(string $zipFilePath): array
    {
        @set_time_limit(900);
        @ini_set('memory_limit', '512M');

        if (!class_exists('ZipArchive')) {
            throw new Exception("PHP ZipArchive extension is not enabled on this server.");
        }

        if (!file_exists($zipFilePath)) {
            throw new Exception("Backup ZIP file not found at: {$zipFilePath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) !== true) {
            throw new Exception("Unable to open the ZIP archive.");
        }

        // Security check: Guard against ZipSlip vulnerability
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (str_contains($entry, '..') || str_starts_with($entry, '/') || str_starts_with($entry, '\\')) {
                $zip->close();
                throw new Exception("Security Alert: Malicious file path detected inside ZIP archive ({$entry}).");
            }
        }

        $publicDir = public_path();
        $targetUploads = public_path('uploads');
        if (!is_dir($targetUploads)) {
            @mkdir($targetUploads, 0755, true);
        }

        // Detect if entries have 'uploads/' prefix
        $firstEntry = $zip->numFiles > 0 ? $zip->getNameIndex(0) : '';
        $hasUploadsPrefix = str_starts_with($firstEntry, 'uploads/') || str_starts_with($firstEntry, 'complaints/');

        $extractPath = $hasUploadsPrefix ? $publicDir : $targetUploads;
        $extracted = $zip->extractTo($extractPath);
        $numFiles = $zip->numFiles;
        $zip->close();

        if (!$extracted) {
            throw new Exception("Failed to extract ZIP archive.");
        }

        // Re-index media library if media table exists
        $this->syncMediaLibrary();

        return [
            'success'         => true,
            'extracted_files' => $numFiles,
        ];
    }

    /**
     * Sync Media database records with extracted files
     */
    public function syncMediaLibrary(): int
    {
        if (!Schema::hasTable('media')) {
            return 0;
        }

        $baseDir = public_path('uploads');
        if (!is_dir($baseDir)) {
            return 0;
        }

        $allowedExtensions = ['webp', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'avif'];
        $existingPaths = Media::pluck('file_path')->flip()->toArray();
        $records = [];
        $now = now();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                continue;
            }

            $realPath = str_replace('\\', '/', $file->getRealPath());
            $pos = strpos($realPath, '/uploads/');
            if ($pos !== false) {
                $relativePath = 'public' . substr($realPath, $pos);
            } else {
                $relativePath = 'public/uploads/' . $file->getFilename();
            }

            if (isset($existingPaths[$relativePath])) {
                continue;
            }

            $folder = 'general';
            $subfolder = null;
            $parts = explode('/', trim(substr($realPath, $pos + 9), '/'));
            if (count($parts) > 1) {
                $folder = $parts[0];
                if (count($parts) > 2) {
                    $subfolder = $parts[1];
                }
            }

            $records[] = [
                'file_name'  => $file->getFilename(),
                'file_path'  => $relativePath,
                'folder'     => $folder,
                'subfolder'  => $subfolder,
                'extension'  => $ext,
                'file_size'  => $file->getSize(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($records) >= 200) {
                Media::insert($records);
                $records = [];
            }
        }

        if (!empty($records)) {
            Media::insert($records);
        }

        return count($records);
    }

    /**
     * Delete image backup file
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
