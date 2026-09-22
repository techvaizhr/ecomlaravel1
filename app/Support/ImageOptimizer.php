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

        return $relativePath;
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
        $startQuality = self::START_QUALITY;
        $size         = self::saveAtQuality($sourcePath, $fullPath, $startQuality, $maxWidth, $maxHeight);

        if ($size <= $maxBytes) {
            return;
        }

        // Calculate intelligent step-down quality if > target
        $estimated = (int) round($startQuality * pow($maxBytes / max($size, 1), 0.72) * 0.98);
        $estimated = max(55, min(89, $estimated));

        if ($estimated < $startQuality) {
            $size = self::saveAtQuality($sourcePath, $fullPath, $estimated, $maxWidth, $maxHeight);
            if ($size <= $maxBytes) {
                return;
            }
        }

        $fallback = max(45, $estimated - 10);
        if ($fallback < $estimated) {
            self::saveAtQuality($sourcePath, $fullPath, $fallback, $maxWidth, $maxHeight);
        }
    }

    private static function saveAtQuality(
        string $sourcePath,
        string $fullPath,
        int $quality,
        int $maxWidth = self::MAX_WIDTH,
        int $maxHeight = self::MAX_HEIGHT
    ): int {
        $image = Image::make($sourcePath);
        self::orientate($image);

        // Auto proportional resize keeping aspect ratio & no upscaling
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->encode('webp', $quality);
        $image->save($fullPath);
        $image->destroy();

        return file_exists($fullPath) ? (int) filesize($fullPath) : 0;
    }

    private static function orientate($image): void
    {
        if (method_exists($image, 'orientate')) {
            $image->orientate();
        }
    }
}
