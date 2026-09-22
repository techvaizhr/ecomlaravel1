<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    /**
     * Display media library with folder filters, search, sorting and per-page select.
     */
    public function index(Request $request)
    {
        $baseDir = base_path('public/uploads');
        if (!File::exists($baseDir)) {
            File::makeDirectory($baseDir, 0755, true);
        }

        $allFiles = File::allFiles($baseDir);
        $allowedExtensions = ['webp', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'avif'];

        $mediaList = [];
        $foldersSummary = [];
        $totalBytes = 0;

        foreach ($allFiles as $file) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                continue;
            }

            $size = $file->getSize();
            $totalBytes += $size;
            $modified = $file->getMTime();
            $filename = $file->getFilename();

            // Relative path like public/uploads/product/2026/09/sample.webp
            $realPath = str_replace('\\', '/', $file->getRealPath());
            $rootPath = str_replace('\\', '/', base_path()) . '/';
            $relativePath = str_replace($rootPath, '', $realPath);

            // Extract primary folder under uploads/
            $relativeUnderUploads = str_replace(str_replace('\\', '/', $baseDir) . '/', '', $realPath);
            $parts = explode('/', $relativeUnderUploads);
            $folderName = count($parts) > 1 ? $parts[0] : 'root';

            if (!isset($foldersSummary[$folderName])) {
                $foldersSummary[$folderName] = 0;
            }
            $foldersSummary[$folderName]++;

            // Fast image dimensions check
            $dimensions = '';
            if (in_array($ext, ['webp', 'jpg', 'jpeg', 'png', 'gif', 'bmp'], true)) {
                $info = @getimagesize($realPath);
                if ($info && !empty($info[0]) && !empty($info[1])) {
                    $dimensions = $info[0] . ' × ' . $info[1];
                }
            }

            $mediaList[] = [
                'name'           => $filename,
                'path'           => $relativePath,
                'url'            => asset($relativePath),
                'folder'         => $folderName,
                'subfolder'      => count($parts) > 2 ? $parts[1] : '',
                'size'           => $size,
                'size_formatted' => $this->formatBytes($size),
                'dimensions'     => $dimensions,
                'extension'      => strtoupper($ext),
                'modified'       => $modified,
                'date_formatted' => date('d M Y, h:i A', $modified),
            ];
        }

        $collection = collect($mediaList);

        // Filter by folder
        $selectedFolder = $request->get('folder', 'all');
        if ($selectedFolder && $selectedFolder !== 'all') {
            $collection = $collection->filter(function ($item) use ($selectedFolder) {
                return strtolower($item['folder']) === strtolower($selectedFolder);
            });
        }

        // Search by filename
        $searchKeyword = trim($request->get('search', ''));
        if ($searchKeyword !== '') {
            $collection = $collection->filter(function ($item) use ($searchKeyword) {
                return stripos($item['name'], $searchKeyword) !== false;
            });
        }

        // Sort items
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $collection = $collection->sortBy('modified');
                break;
            case 'largest':
                $collection = $collection->sortByDesc('size');
                break;
            case 'smallest':
                $collection = $collection->sortBy('size');
                break;
            case 'name_asc':
                $collection = $collection->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            case 'name_desc':
                $collection = $collection->sortByDesc('name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            case 'newest':
            default:
                $collection = $collection->sortByDesc('modified');
                break;
        }

        // Per page items
        $allowedPerPage = [12, 24, 48, 96, 150];
        $perPage = (int) $request->get('per_page', 24);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 24;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();

        $paginatedMedia = new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $stats = [
            'total_files' => count($mediaList),
            'total_size'  => $this->formatBytes($totalBytes),
            'filtered'    => $collection->count(),
        ];

        ksort($foldersSummary);

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
     * Delete a single image from disk.
     */
    public function destroy(Request $request)
    {
        $filePath = $request->get('file_path');
        if (!$filePath) {
            return response()->json(['success' => false, 'message' => 'ফাইল পাথ পাওয়া যায়নি।'], 400);
        }

        $deleted = $this->deleteSecurely($filePath);

        if ($deleted) {
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
     * Delete multiple selected images from disk.
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

        $deletedCount = 0;
        foreach ($files as $file) {
            if ($this->deleteSecurely($file)) {
                $deletedCount++;
            }
        }

        $message = "সফলভাবে {$deletedCount} টি ইমেজ ডিলিট করা হয়েছে।";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $deletedCount,
                'message' => $message,
            ]);
        }

        Toastr::success($message, 'সফল');
        return redirect()->back();
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
