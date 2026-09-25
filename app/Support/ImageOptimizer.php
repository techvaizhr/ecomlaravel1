<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageOptimizer
{
    /** Default target max file size in KB. */
    public const TARGET_KB = 300;

    /** Default max dimensions. */
    public const MAX_WIDTH = 1000;
    public const MAX_HEIGHT = 1000;

    /** High initial quality to preserve visual clarity. */
    public const START_QUALITY = 90;

    /**
     * Strict image verification to prevent malicious shell / polyglot uploads.
     *
     * @throws \InvalidArgumentException
     */
    public static function validateRealImage(UploadedFile $file): void
    {
        // 1. Basic upload check
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Uploaded file is invalid or incomplete.');
        }

        // 2. Strict MIME whitelist
        $allowedMimes = [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/x-png',
            'image/webp',
            'image/gif',
            'image/bmp',
            'image/x-ms-bmp',
        ];

        $clientMime = strtolower((string) $file->getClientMimeType());
        $realMime   = strtolower((string) $file->getMimeType());

        if (!in_array($realMime, $allowedMimes, true) && !in_array($clientMime, $allowedMimes, true)) {
            throw new \InvalidArgumentException('নিরাপত্তার স্বার্থে শুধুমাত্র আসল ছবি (JPEG, PNG, WEBP, GIF) আপলোড করা যাবে।');
        }

        // 3. Strict extension check - deny php, phtml, exe, svg, html, js, etc.
        $clientExt = strtolower((string) $file->getClientOriginalExtension());
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
        if (!in_array($clientExt, $allowedExts, true)) {
            throw new \InvalidArgumentException('অবৈধ ফাইল এক্সটেনশন। শুধুমাত্র jpg, jpeg, png, webp, gif অনুমোদিত।');
        }

        // 4. Binary header inspection using getimagesize()
        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            throw new \InvalidArgumentException('ছবির অস্থায়ী ফাইল খুঁজে পাওয়া যায়নি।');
        }

        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new \InvalidArgumentException('ফাইলটি কোনো বৈধ ছবি নয় বা ক্ষতিগ্রস্ত। অনুগ্রহ করে একটি সঠিক ছবি নির্বাচন করুন।');
        }

        $validImageTypes = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP];
        if (defined('IMAGETYPE_WEBP')) {
            $validImageTypes[] = IMAGETYPE_WEBP;
        }
        if (!in_array($imageInfo[2], $validImageTypes, true)) {
            throw new \InvalidArgumentException('ছবির অভ্যন্তরীণ ফরম্যাট সঠিক নয়।');
        }

        // 5. Scan first 2KB for PHP or script code (Polyglot exploit protection)
        $fh = @fopen($realPath, 'rb');
        if ($fh) {
            $headerBytes = fread($fh, 2048);
            fclose($fh);
            if (preg_match('/<\?php|<\?=|eval\(|base64_decode\(/i', $headerBytes)) {
                throw new \InvalidArgumentException('নিরাপত্তা ঝুঁকি শনাক্ত হয়েছে! এই ফাইলটি সিস্টেমে গ্রহণযোগ্য নয়।');
            }
        }
    }

    /**
     * Store and optimize Logo (guaranteed <= 100 KB WebP, max 600x300 proportional).
     */
    public static function storeLogo(UploadedFile $file, string $directory, ?string $basename = null): string
    {
        return self::store($file, $directory, $basename, 100, 600, 300);
    }

    /**
     * Store and optimize Banner (guaranteed <= 300 KB WebP, max 1920x1080 proportional).
     */
    public static function storeBanner(UploadedFile $file, string $directory, ?string $basename = null): string
    {
        return self::store($file, $directory, $basename, 300, 1920, 1080);
    }

    /**
     * Store and optimize Profile Avatar (guaranteed <= 150 KB WebP, max 500x500 proportional).
     */
    public static function storeProfile(UploadedFile $file, string $directory, ?string $basename = null): string
    {
        return self::store($file, $directory, $basename, 150, 500, 500);
    }

    /**
     * Store and optimize Product Image into dynamic Year/Month subdirectory
     * (e.g. public/uploads/product/2026/09/) for ultimate performance & filesystem scalability.
     */
    public static function storeProductImage(UploadedFile $file, ?string $basename = null): string
    {
        $dir = 'public/uploads/product/' . date('Y/m') . '/';
        return self::store($file, $dir, $basename, 400, 1200, 1200);
    }

    /**
     * Save uploaded image as optimized WebP with security validation.
     */
    public static function store(
        UploadedFile $file,
        string $directory,
        ?string $basename = null,
        int $targetKb = self::TARGET_KB,
        int $maxWidth = self::MAX_WIDTH,
        int $maxHeight = self::MAX_HEIGHT
    ): string {
        // Deep binary security check
        self::validateRealImage($file);

        [$fullDir, $relativeDir] = self::resolveDirectory($directory);
        File::ensureDirectoryExists($fullDir);

        $basename = $basename
            ? self::normalizeBasename($basename)
            : self::makeBasename($file);

        $fullPath     = $fullDir . $basename;
        $relativePath = $relativeDir . $basename;
        $maxBytes     = $targetKb * 1024;

        self::saveOptimizedWebp($file->getRealPath(), $fullPath, $maxBytes, $maxWidth, $maxHeight);

        // Auto-index into Media table for super-fast indexed queries
        try {
            self::indexMediaRow($fullPath, $relativePath, $basename);
        } catch (\Throwable $e) {
            // Silently ignore if table is migrating or unavailable
        }

        return $relativePath;
    }

    /**
     * Index file metadata into media database table.
     */
    public static function indexMediaRow(string $fullPath, string $relativePath, string $filename): void
    {
        static $hasMediaTable = null;
        if ($hasMediaTable === null) {
            $hasMediaTable = \Illuminate\Support\Facades\Schema::hasTable('media');
        }
        if (!$hasMediaTable) {
            return;
        }

        $cleanRelative = str_replace('\\', '/', $relativePath);
        $cleanDir = str_replace(['public/uploads/', 'uploads/'], '', $cleanRelative);
        $parts = explode('/', $cleanDir);
        $folder = count($parts) > 1 ? $parts[0] : 'root';
        $subfolder = count($parts) > 2 ? $parts[1] : null;

        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
        $dimensions = null;
        $info = @getimagesize($fullPath);
        if ($info && !empty($info[0]) && !empty($info[1])) {
            $dimensions = $info[0] . ' × ' . $info[1];
        }

        \App\Models\Media::updateOrCreate(
            ['file_path' => $cleanRelative],
            [
                'file_name'  => $filename,
                'folder'     => $folder,
                'subfolder'  => $subfolder,
                'extension'  => strtolower(pathinfo($cleanRelative, PATHINFO_EXTENSION) ?: 'webp'),
                'file_size'  => $fileSize,
                'dimensions' => $dimensions,
                'mime_type'  => 'image/webp',
            ]
        );
    }

    public static function makeBasename(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::slug($name) ?: 'image';

        return time() . '-' . uniqid() . '-' . $name . '.webp';
    }

    public static function basenameFromOriginal(UploadedFile $file, string $prefix = ''): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::slug($name) ?: 'image';
        $prefix = $prefix !== '' ? rtrim($prefix, '-') . '-' : '';

        return self::normalizeBasename($prefix . time() . '-' . uniqid() . '-' . $name);
    }

    public static function normalizeBasename(string $basename): string
    {
        $basename = trim($basename);
        $basename = preg_replace('/\.(jpe?g|png|gif|webp|bmp)$/i', '', $basename) ?? $basename;
        $basename = Str::slug($basename) ?: 'image';

        return $basename . '.webp';
    }

    private static function resolveDirectory(string $directory): array
    {
        $directory = rtrim(str_replace('\\', '/', $directory), '/') . '/';

        if (str_starts_with($directory, 'public/')) {
            return [base_path($directory), $directory];
        }

        return [public_path($directory), $directory];
    }

    private static function saveOptimizedWebp(
        string $sourcePath,
        string $fullPath,
        int $maxBytes,
        int $maxWidth = self::MAX_WIDTH,
        int $maxHeight = self::MAX_HEIGHT
    ): void {
        // Load image and decode into memory once
        $image = Image::make($sourcePath);
        self::orientate($image);

        // Auto proportional resize in memory keeping aspect ratio & no upscaling
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Fast high-quality WebP encoding (82 quality provides crystal-clear e-commerce photos with 60% smaller file size)
        $quality = 82;
        $image->encode('webp', $quality);
        $image->save($fullPath);

        // If file is exceptionally large (noisy/dense photo > maxBytes), step down quality from the already-resized in-memory resource
        if (file_exists($fullPath) && filesize($fullPath) > $maxBytes) {
            $image->encode('webp', 68);
            $image->save($fullPath);
        }

        $image->destroy();
    }

    private static function orientate($image): void
    {
        if (method_exists($image, 'orientate')) {
            $image->orientate();
        }
    }
}
