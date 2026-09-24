<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Backup\DatabaseBackupService;
use App\Services\Backup\MediaBackupService;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Exception;

class BackupController extends Controller
{
    protected DatabaseBackupService $dbService;
    protected MediaBackupService $mediaService;

    public function __construct(DatabaseBackupService $dbService, MediaBackupService $mediaService)
    {
        $this->dbService = $dbService;
        $this->mediaService = $mediaService;
    }

    /**
     * Display backup dashboard with DB & Images backups
     */
    public function index()
    {
        $dbBackups = $this->dbService->getBackupsList();
        $imageBackups = $this->mediaService->getBackupsList();
        $uploadsStats = $this->mediaService->getCurrentUploadsStats();

        // Database stats
        $dbStats = [
            'tables_count' => 0,
            'size_bytes'   => 0,
            'size_formatted' => '0 B',
        ];

        try {
            $dbName = config('database.connections.mysql.database');
            $tablesData = DB::select("SELECT COUNT(*) as tbl_count, SUM(data_length + index_length) as total_size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            if (!empty($tablesData)) {
                $totalBytes = (int)($tablesData[0]->total_size ?? 0);
                $dbStats['tables_count'] = (int)($tablesData[0]->tbl_count ?? 0);
                $dbStats['size_bytes'] = $totalBytes;
                $dbStats['size_formatted'] = $this->formatBytes($totalBytes);
            }
        } catch (Exception $e) {}

        return view('backEnd.backup.index', compact(
            'dbBackups',
            'imageBackups',
            'uploadsStats',
            'dbStats'
        ));
    }

    /**
     * Create new Database Backup
     */
    public function createDb(Request $request)
    {
        try {
            $compress = $request->boolean('compress', false);
            $result = $this->dbService->createBackup($compress);

            Toastr::success("ডাটাবেজ ব্যাকআপ সফলভাবে তৈরি হয়েছে! ফাইল: {$result['filename']} ({$result['size_formatted']})");
        } catch (Exception $e) {
            Toastr::error("ডাটাবেজ ব্যাকআপ তৈরিতে ত্রুটি: " . $e->getMessage());
        }

        return redirect()->route('admin.backups.index', ['tab' => 'db']);
    }

    /**
     * Download Database Backup
     */
    public function downloadDb($filename)
    {
        $cleanFilename = basename($filename);
        $filePath = $this->dbService->getBackupDir() . DIRECTORY_SEPARATOR . $cleanFilename;

        if (!file_exists($filePath)) {
            Toastr::error("ব্যাকআপ ফাইলটি পাওয়া যায়নি।");
            return redirect()->back();
        }

        return response()->download($filePath, $cleanFilename);
    }

    /**
     * Restore Database from existing backup or uploaded file
     */
    public function restoreDb(Request $request)
    {
        $this->validate($request, [
            'filename'    => 'nullable|string',
            'backup_file' => 'nullable|file|max:524288', // 512MB max
        ]);

        $targetPath = null;
        $tempUploaded = false;

        try {
            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $ext = strtolower($file->getClientOriginalExtension());

                if (!in_array($ext, ['sql', 'gz'], true)) {
                    Toastr::error("শুধুমাত্র .sql অথবা .sql.gz ফাইল আপলোড করা যাবে।");
                    return redirect()->back();
                }

                $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.' . $ext;
                $targetPath = $this->dbService->getBackupDir() . DIRECTORY_SEPARATOR . $filename;
                $file->move($this->dbService->getBackupDir(), $filename);
            } elseif ($request->filled('filename')) {
                $cleanFilename = basename($request->filename);
                $targetPath = $this->dbService->getBackupDir() . DIRECTORY_SEPARATOR . $cleanFilename;
            } else {
                Toastr::error("দয়া করে একটি ব্যাকআপ ফাইল নির্বাচন বা আপলোড করুন।");
                return redirect()->back();
            }

            if (!file_exists($targetPath)) {
                Toastr::error("ব্যাকআপ ফাইলটি পাওয়া যায়নি।");
                return redirect()->back();
            }

            $result = $this->dbService->restoreBackup($targetPath);

            Toastr::success("ডাটাবেজ সফলভাবে রিস্টোর হয়েছে! মোট {$result['executed_statements']} টি কুয়েরি কার্যকর করা হয়েছে।");
        } catch (Exception $e) {
            Toastr::error("ডাটাবেজ রিস্টোরে ত্রুটি: " . $e->getMessage());
        }

        return redirect()->route('admin.backups.index', ['tab' => 'db']);
    }

    /**
     * Delete Database Backup
     */
    public function deleteDb(Request $request)
    {
        $this->validate($request, [
            'filename' => 'required|string',
        ]);

        try {
            $deleted = $this->dbService->deleteBackup($request->filename);
            if ($deleted) {
                Toastr::success("ডাটাবেজ ব্যাকআপ ফাইল সফলভাবে মুছে ফেলা হয়েছে।");
            } else {
                Toastr::error("ফাইলটি মুছে ফেলা সম্ভব হয়নি।");
            }
        } catch (Exception $e) {
            Toastr::error("ত্রুটি: " . $e->getMessage());
        }

        return redirect()->route('admin.backups.index', ['tab' => 'db']);
    }

    /**
     * Create new Images & Media Backup (ZIP)
     */
    public function createImages()
    {
        try {
            $result = $this->mediaService->createBackup();

            Toastr::success("সব ছবির জিপ ব্যাকআপ সফলভাবে তৈরি হয়েছে! মোট {$result['files_count']} টি ছবি সংরক্ষিত ({$result['size_formatted']})");
        } catch (Exception $e) {
            Toastr::error("ছবি ব্যাকআপ তৈরিতে ত্রুটি: " . $e->getMessage());
        }

        return redirect()->route('admin.backups.index', ['tab' => 'images']);
    }

    /**
     * Download Images Backup
     */
    public function downloadImages($filename)
    {
        $cleanFilename = basename($filename);
        $filePath = $this->mediaService->getBackupDir() . DIRECTORY_SEPARATOR . $cleanFilename;

        if (!file_exists($filePath)) {
            Toastr::error("ব্যাকআপ ফাইলটি পাওয়া যায়নি।");
            return redirect()->back();
        }

        return response()->download($filePath, $cleanFilename);
    }

    /**
     * Restore Images from existing backup or uploaded ZIP
     */
    public function restoreImages(Request $request)
    {
        $this->validate($request, [
            'filename'    => 'nullable|string',
            'backup_file' => 'nullable|file|mimes:zip|max:1048576', // 1GB max
        ]);

        $targetPath = null;

        try {
            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.zip';
                $targetPath = $this->mediaService->getBackupDir() . DIRECTORY_SEPARATOR . $filename;
                $file->move($this->mediaService->getBackupDir(), $filename);
            } elseif ($request->filled('filename')) {
                $cleanFilename = basename($request->filename);
                $targetPath = $this->mediaService->getBackupDir() . DIRECTORY_SEPARATOR . $cleanFilename;
            } else {
                Toastr::error("দয়া করে একটি জিপ ব্যাকআপ ফাইল নির্বাচন বা আপলোড করুন।");
                return redirect()->back();
            }

            if (!file_exists($targetPath)) {
                Toastr::error("ব্যাকআপ ফাইলটি পাওয়া যায়নি।");
                return redirect()->back();
            }

            $result = $this->mediaService->restoreBackup($targetPath);

            Toastr::success("সব ছবি সফলভাবে রিস্টোর হয়েছে! মোট {$result['extracted_files']} টি ফাইল পুনরুদ্ধার করা হয়েছে।");
        } catch (Exception $e) {
            Toastr::error("ছবি রিস্টোরে ত্রুটি: " . $e->getMessage());
        }

        return redirect()->route('admin.backups.index', ['tab' => 'images']);
    }

    /**
     * Delete Images Backup
     */
    public function deleteImages(Request $request)
    {
        $this->validate($request, [
            'filename' => 'required|string',
        ]);

        try {
            $deleted = $this->mediaService->deleteBackup($request->filename);
            if ($deleted) {
                Toastr::success("ইমেজ ব্যাকআপ ফাইল সফলভাবে মুছে ফেলা হয়েছে।");
            } else {
                Toastr::error("ফাইলটি মুছে ফেলা সম্ভব হয়নি।");
            }
        } catch (Exception $e) {
            Toastr::error("ত্রুটি: " . $e->getMessage());
        }

        return redirect()->route('admin.backups.index', ['tab' => 'images']);
    }

    /**
     * Helper to format bytes
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int)floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
