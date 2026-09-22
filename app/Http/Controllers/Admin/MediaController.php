<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\ImageOptimizer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MediaController extends Controller
{
    /**
     * Display media library using high-performance indexed database queries.
     */
    public function index(Request $request)
    {
        // If media table is empty on first visit, run an initial fast sync of uploads
        if (Schema::hasTable('media') && Media::count() === 0) {
            $this->performFastFilesystemSync();
        }

        $query = Media::query();

        // 1. Filter by folder
        $selectedFolder = $request->get('folder', 'all');
        if ($selectedFolder && $selectedFolder !== 'all') {
            $query->where('folder', $selectedFolder);
        }

        // 2. Search by filename
        $searchKeyword = trim($request->get('search', ''));
        if ($searchKeyword !== '') {
            $query->where('file_name', 'like', "%{$searchKeyword}%");
        }

        // 3. Sorting (Database indexed)
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'largest':
                $query->orderBy('file_size', 'desc');
                break;
            case 'smallest':
                $query->orderBy('file_size', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('file_name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // 4. Per page items
        $allowedPerPage = [12, 24, 48, 96, 150];
        $perPage = (int) $request->get('per_page', 24);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 24;
        }

        $paginatedMedia = $query->paginate($perPage)->withQueryString();

        // 5. Fast indexed summary queries
        $foldersSummary = [];
        if (Schema::hasTable('media')) {
            $foldersSummary = Media::select('folder', DB::raw('count(*) as count'))
                ->groupBy('folder')
                ->pluck('count', 'folder')
                ->toArray();
            ksort($foldersSummary);
        }

        $totalFiles = Schema::hasTable('media') ? Media::count() : 0;
        $totalBytes = Schema::hasTable('media') ? (int) Media::sum('file_size') : 0;

        $stats = [
            'total_files' => $totalFiles,
            'total_size'  => $this->formatBytes($totalBytes),
            'filtered'    => $paginatedMedia->total(),
        ];

        return view('backEnd.media.index', compact(
            'paginatedMedia',
            'foldersSummary',
            'stats',
            'selectedFolder',
            'searchKeyword',
            'sort',
            'perPage'
        ));
    }

    /**
     * Upload single or multiple images with security validation and WebP optimization.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'images'   => 'required',
            'folder'   => 'nullable|string',
        ]);

        $folder = $request->get('folder', 'product');
        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploadedPaths = [];
        $failedCount = 0;

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                $failedCount++;
                continue;
            }

            try {
                // Route to appropriate preset
                switch ($folder) {
                    case 'banner':
                        $path = ImageOptimizer::storeBanner($file, 'public/uploads/banner/');
                        break;
                    case 'brand':
                    case 'logo':
                        $path = ImageOptimizer::storeLogo($file, 'public/uploads/brand/');
                        break;
                    case 'category':
                        $path = ImageOptimizer::store($file, 'public/uploads/category/');
                        break;
                    case 'settings':
                        $path = ImageOptimizer::store($file, 'public/uploads/settings/');
                        break;
                    case 'product':
                    default:
                        $path = ImageOptimizer::storeProductImage($file);
                        break;
                }

                $uploadedPaths[] = [
                    'path' => $path,
                    'url'  => asset($path),
                ];
            } catch (\Throwable $e) {
                $failedCount++;
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => count($uploadedPaths) > 0,
                'uploaded' => $uploadedPaths,
                'failed'   => $failedCount,
                'message'  => count($uploadedPaths) . ' টি ফাইল সফলভাবে আপলোড ও WebP-তে অপ্টিমাইজ করা হয়েছে।'
            ]);
        }

        if (count($uploadedPaths) > 0) {
            Toastr::success(count($uploadedPaths) . ' টি ইমেজ সফলভাবে অপ্টিমাইজড হয়ে আপলোড হয়েছে।', 'সফল');
        }
        if ($failedCount > 0) {
            Toastr::warning($failedCount . ' টি ফাইল আপলোডে ব্যর্থ হয়েছে বা নিরাপত্তার কারণে বাতিল হয়েছে।', 'সতর্কতা');
        }

        return redirect()->back();
    }

    /**
     * Delete a single image from disk and database index.
     */
    public function destroy(Request $request)
    {
        $filePath = $request->get('file_path');
        if (!$filePath) {
            return response()->json(['success' => false, 'message' => 'ফাইল পাথ পাওয়া যায়নি।'], 400);
        }

        $deleted = $this->deleteSecurely($filePath);

        if ($deleted) {
            if (Schema::hasTable('media')) {
                Media::where('file_path', $filePath)->delete();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'ফাইলটি সফলভাবে ডিলিট করা হয়েছে।']);
            }
            Toastr::success('ফাইলটি সফলভাবে ডিলিট করা হয়েছে।', 'সফল');
            return redirect()->back();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'ফাইল খুঁজে পাওয়া যায়নি বা ডিলিট করা যায়নি।'], 404);
        }
        Toastr::error('ফাইল খুঁজে পাওয়া যায়নি বা ডিলিট করা যায়নি।', 'ব্যর্থ');
        return redirect()->back();
    }

    /**
     * Delete multiple selected images from disk and database index.
     */
    public function bulkDestroy(Request $request)
    {
        $files = $request->get('files', []);
        if (!is_array($files) || empty($files)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'কোনো ফাইল সিলেক্ট করা হয়নি।'], 400);
            }
            Toastr::error('কোনো ফাইল সিলেক্ট করা হয়নি।', 'ত্রুটি');
            return redirect()->back();
        }

        $deletedPaths = [];
        foreach ($files as $file) {
            if ($this->deleteSecurely($file)) {
                $deletedPaths[] = $file;
            }
        }

        if (count($deletedPaths) > 0 && Schema::hasTable('media')) {
            Media::whereIn('file_path', $deletedPaths)->delete();
        }

        $count = count($deletedPaths);
        $message = "সফলভাবে {$count} টি ইমেজ ডিলিট করা হয়েছে।";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => $message,
            ]);
        }

        Toastr::success($message, 'সফল');
        return redirect()->back();
    }

    /**
     * 1-Click Sync / Reindex: Scans uploads folder and populates/refreshes the indexed media database.
     */
    public function sync(Request $request)
    {
        $count = $this->performFastFilesystemSync();

        Toastr::success("মিডিয়া লাইব্রেরি সফলভাবে সিঙ্ক সম্পন্ন হয়েছে! মোট {$count} টি ইমেজ ডাটাবেজে ইনডেক্স করা হয়েছে।", 'সুপার ফাস্ট');
        return redirect()->back();
    }

    /**
     * Fast batch indexing of all image files in uploads folder.
     */
    private function performFastFilesystemSync(): int
    {
        if (!Schema::hasTable('media')) {
            return 0;
        }

        $baseDir = base_path('public/uploads');
        if (!File::exists($baseDir)) {
            File::makeDirectory($baseDir, 0755, true);
            return 0;
        }

        $allowedExtensions = ['webp', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'avif'];
        $allFiles = File::allFiles($baseDir);

        $rootPath = str_replace('\\', '/', base_path()) . '/';
        $baseUploadsPath = str_replace('\\', '/', $baseDir) . '/';

        $records = [];
        $existingPaths = Media::pluck('file_path')->flip()->toArray();
        $now = now();

        foreach ($allFiles as $file) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                continue;
            }

            $realPath = str_replace('\\', '/', $file->getRealPath());
            $relativePath = str_replace($rootPath, '', $realPath);

            // Skip if already in database
            if (isset($existingPaths[$relativePath])) {
                continue;
            }

            $relativeUnderUploads = str_replace($baseUploadsPath, '', $realPath);
            $parts = explode('/', $relativeUnderUploads);
            $folderName = count($parts) > 1 ? $parts[0] : 'root';
            $subfolderName = count($parts) > 2 ? $parts[1] : null;

            $dimensions = null;
            if (in_array($ext, ['webp', 'jpg', 'jpeg', 'png', 'gif', 'bmp'], true)) {
                $info = @getimagesize($realPath);
                if ($info && !empty($info[0]) && !empty($info[1])) {
                    $dimensions = $info[0] . ' × ' . $info[1];
                }
            }

            $records[] = [
                'file_name'  => $file->getFilename(),
                'file_path'  => $relativePath,
                'folder'     => $folderName,
                'subfolder'  => $subfolderName,
                'extension'  => $ext,
                'file_size'  => $file->getSize(),
                'dimensions' => $dimensions,
                'mime_type'  => 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext),
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'updated_at' => $now,
            ];

            // Batch insert in chunks of 250 for speed and low memory
            if (count($records) >= 250) {
                Media::insertOrIgnore($records);
                $records = [];
            }
        }

        if (count($records) > 0) {
            Media::insertOrIgnore($records);
        }

        return Media::count();
    }

    /**
     * Verify path against directory traversal and delete file securely.
     */
    private function deleteSecurely(string $rawPath): bool
    {
        $cleanPath = str_replace('\\', '/', trim($rawPath));

        // Prevent directory traversal exploits
        if (str_contains($cleanPath, '..') || str_contains($cleanPath, ':') || str_contains($cleanPath, "\0")) {
            return false;
        }

        // Must strictly reside inside public/uploads or uploads
        if (!str_starts_with($cleanPath, 'public/uploads/') && !str_starts_with($cleanPath, 'uploads/')) {
            return false;
        }

        $fullPath = base_path($cleanPath);
        if (File::exists($fullPath) && !File::isDirectory($fullPath)) {
            return @unlink($fullPath);
        }

        // Also check public_path fallback
        $publicPath = public_path(str_replace('public/', '', $cleanPath));
        if (File::exists($publicPath) && !File::isDirectory($publicPath)) {
            return @unlink($publicPath);
        }

        return false;
    }

    /**
     * Format bytes to readable size string.
     */
    private function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / pow(1024, $power), $precision) . ' ' . $units[$power];
    }
}
