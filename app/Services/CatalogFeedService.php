<?php

namespace App\Services;

use App\Models\Product;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Cache;

class CatalogFeedService
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    const CACHE_TTL = 3600;

    /**
     * Detect dynamically the current scheme & host/domain
     * Works with HTTP, HTTPS, custom domains, proxies, and multi-domains
     */
    public function getBaseUrl(): string
    {
        if (request() && request()->getHttpHost()) {
            $scheme = (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https' || request()->server('HTTPS') === 'on')
                ? 'https'
                : request()->getScheme();

            return rtrim($scheme . '://' . request()->getHttpHost(), '/');
        }

        $appUrl = config('app.url', 'http://localhost');
        return rtrim($appUrl, '/');
    }

    /**
     * Generate standard XML RSS 2.0 Feed for Facebook & Google Catalog
     * Fully dynamic domain support & host-specific caching
     *
     * @param string $feedType 'facebook' | 'google' | 'universal'
     * @param bool $forceRefresh
     * @return string
     */
    public function getXmlFeed(string $feedType = 'facebook', bool $forceRefresh = false): string
    {
        $baseUrl = $this->getBaseUrl();
        $hostHash = substr(md5($baseUrl), 0, 8);
        $cacheKey = "catalog_feed_xml_{$feedType}_{$hostHash}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($feedType, $baseUrl) {
            return $this->buildXmlFeed($feedType, $baseUrl);
        });
    }

    /**
     * Generate CSV Feed for Facebook Catalog
     * Fully dynamic domain support & host-specific caching
     *
     * @param bool $forceRefresh
     * @return string
     */
    public function getCsvFeed(bool $forceRefresh = false): string
    {
        $baseUrl = $this->getBaseUrl();
        $hostHash = substr(md5($baseUrl), 0, 8);
        $cacheKey = "catalog_feed_csv_facebook_{$hostHash}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($baseUrl) {
            return $this->buildCsvFeed($baseUrl);
        });
    }

    /**
     * Clear all catalog feed caches across domains
     */
    public static function clearCache(): void
    {
        Cache::forget('catalog_feed_xml_facebook');
        Cache::forget('catalog_feed_xml_google');
        Cache::forget('catalog_feed_xml_universal');
        Cache::forget('catalog_feed_csv_facebook');

        if (request() && request()->getHttpHost()) {
            $scheme = (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https' || request()->server('HTTPS') === 'on')
                ? 'https'
                : request()->getScheme();
            $baseUrl = rtrim($scheme . '://' . request()->getHttpHost(), '/');
            $hostHash = substr(md5($baseUrl), 0, 8);

            Cache::forget("catalog_feed_xml_facebook_{$hostHash}");
            Cache::forget("catalog_feed_xml_google_{$hostHash}");
            Cache::forget("catalog_feed_xml_universal_{$hostHash}");
            Cache::forget("catalog_feed_csv_facebook_{$hostHash}");
        }
    }

    /**
     * Build XML feed string using dynamic domain
     */
    protected function buildXmlFeed(string $feedType, string $baseUrl): string
    {
        $setting = GeneralSetting::first();
        $storeName = $setting->name ?? config('app.name', 'Online Store');
        $currency = $this->getCurrency($setting);

        $items = $this->prepareFeedItems($currency, $storeName, $baseUrl);

        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">';
        $xml[] = '  <channel>';
        $xml[] = '    <title>' . $this->cdata($storeName . ' Product Catalog') . '</title>';
        $xml[] = '    <link>' . htmlspecialchars($baseUrl, ENT_XML1, 'UTF-8') . '</link>';
        $xml[] = '    <description>' . $this->cdata($storeName . ' Product Feed for Facebook Catalog & Google Merchant Center') . '</description>';

        foreach ($items as $item) {
            $xml[] = '    <item>';
            $xml[] = '      <g:id>' . htmlspecialchars($item['id'], ENT_XML1, 'UTF-8') . '</g:id>';
            $xml[] = '      <g:title>' . $this->cdata($item['title']) . '</g:title>';
            $xml[] = '      <g:description>' . $this->cdata($item['description']) . '</g:description>';
            $xml[] = '      <g:link>' . htmlspecialchars($item['link'], ENT_XML1, 'UTF-8') . '</g:link>';
            $xml[] = '      <g:image_link>' . htmlspecialchars($item['image_link'], ENT_XML1, 'UTF-8') . '</g:image_link>';

            if (!empty($item['additional_images'])) {
                foreach ($item['additional_images'] as $addImg) {
                    $xml[] = '      <g:additional_image_link>' . htmlspecialchars($addImg, ENT_XML1, 'UTF-8') . '</g:additional_image_link>';
                }
            }

            $xml[] = '      <g:availability>' . $item['availability'] . '</g:availability>';
            $xml[] = '      <g:price>' . $item['price'] . '</g:price>';

            if (!empty($item['sale_price'])) {
                $xml[] = '      <g:sale_price>' . $item['sale_price'] . '</g:sale_price>';
            }

            $xml[] = '      <g:brand>' . $this->cdata($item['brand']) . '</g:brand>';
            $xml[] = '      <g:condition>' . $item['condition'] . '</g:condition>';

            if (!empty($item['item_group_id'])) {
                $xml[] = '      <g:item_group_id>' . htmlspecialchars($item['item_group_id'], ENT_XML1, 'UTF-8') . '</g:item_group_id>';
            }

            if (!empty($item['color'])) {
                $xml[] = '      <g:color>' . $this->cdata($item['color']) . '</g:color>';
            }

            if (!empty($item['size'])) {
                $xml[] = '      <g:size>' . $this->cdata($item['size']) . '</g:size>';
            }

            if (!empty($item['product_type'])) {
                $xml[] = '      <g:product_type>' . $this->cdata($item['product_type']) . '</g:product_type>';
            }

            if (!empty($item['google_product_category'])) {
                $xml[] = '      <g:google_product_category>' . $this->cdata($item['google_product_category']) . '</g:google_product_category>';
            }

            $xml[] = '    </item>';
        }

        $xml[] = '  </channel>';
        $xml[] = '</rss>';

        return implode("\n", $xml);
    }

    /**
     * Build CSV feed string for Facebook Catalog with dynamic domain
     */
    protected function buildCsvFeed(string $baseUrl): string
    {
        $setting = GeneralSetting::first();
        $storeName = $setting->name ?? config('app.name', 'Online Store');
        $currency = $this->getCurrency($setting);

        $items = $this->prepareFeedItems($currency, $storeName, $baseUrl);

        $handle = fopen('php://temp', 'r+');

        // CSV Header compatible with Facebook Commerce Manager Catalog
        fputcsv($handle, [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'sale_price',
            'link',
            'image_link',
            'brand',
            'item_group_id',
            'color',
            'size',
            'product_type',
            'google_product_category'
        ]);

        foreach ($items as $item) {
            fputcsv($handle, [
                $item['id'],
                $item['title'],
                $item['description'],
                $item['availability'],
                $item['condition'],
                $item['price'],
                $item['sale_price'] ?? '',
                $item['link'],
                $item['image_link'],
                $item['brand'],
                $item['item_group_id'] ?? '',
                $item['color'] ?? '',
                $item['size'] ?? '',
                $item['product_type'] ?? '',
                $item['google_product_category'] ?? ''
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Prepare catalog items list with dynamic domain URLs
     *
     * @param string $currency
     * @param string $storeName
     * @param string $baseUrl
     * @return array
     */
    protected function prepareFeedItems(string $currency, string $storeName, string $baseUrl): array
    {
        $products = Product::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('approval_status')
                  ->orWhere('approval_status', 'approved');
            })
            ->with([
                'image',
                'images',
                'category',
                'subcategory',
                'childcategory',
                'brand',
                'variantPrices.color',
                'variantPrices.size',
                'colors',
                'sizes'
            ])
            ->orderBy('id', 'desc')
            ->get();

        $feedItems = [];

        foreach ($products as $product) {
            // Main image resolution (single product & fallback for variants)
            $mainImageUrl = $this->getMainImageUrl($product, $baseUrl);

            // Clean description
            $description = $this->cleanDescription($product->description ?? $product->meta_description ?? $product->name);

            // Product category path / type
            $categoryPath = $this->getCategoryPath($product);
            $googleCategory = $product->category->name ?? 'Apparel & Accessories';

            // Brand
            $brand = !empty($product->brand?->name) ? $product->brand->name : $storeName;

            // Canonical link with dynamic domain
            $canonicalLink = $baseUrl . '/product/' . $product->slug;

            // Additional images (excluding main image)
            $additionalImages = $this->getAdditionalImages($product, $mainImageUrl, $baseUrl);

            // Check if product has variations
            $variants = $this->extractProductVariants($product);

            if (!empty($variants)) {
                // VARIATION PRODUCT:
                // Export each variant as a separate item linked via item_group_id
                foreach ($variants as $variant) {
                    $variantAttrs = array_filter([$variant['color_name'], $variant['size_name']]);
                    $variantTitle = $product->name . (!empty($variantAttrs) ? ' - ' . implode(' / ', $variantAttrs) : '');

                    // Price & Sale Price
                    $varPrice = $variant['price'];
                    $origPrice = ($product->old_price && $product->old_price > $varPrice) ? (float)$product->old_price : $varPrice;
                    $salePrice = ($origPrice > $varPrice) ? $varPrice : null;

                    $formattedPrice = number_format($origPrice, 2, '.', '') . ' ' . $currency;
                    $formattedSalePrice = $salePrice ? (number_format($salePrice, 2, '.', '') . ' ' . $currency) : null;

                    // Image Resolution:
                    // Variation image matching variation value, fallback to main image if not found
                    $variantImage = $this->getVariantImageUrl(
                        $product,
                        $variant['color_id'],
                        $variant['size_id'],
                        $mainImageUrl,
                        $baseUrl
                    );

                    // Deep link with variant query parameters and dynamic domain
                    $queryParams = [];
                    if (!empty($variant['color_id'])) {
                        $queryParams['color'] = $variant['color_id'];
                    }
                    if (!empty($variant['size_id'])) {
                        $queryParams['size'] = $variant['size_id'];
                    }
                    $variantLink = $canonicalLink . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');

                    // Availability
                    $isAvailable = $variant['stock'] !== null ? ($variant['stock'] > 0) : ($product->stock > 0);
                    $availability = $isAvailable ? 'in stock' : 'out of stock';

                    $feedItems[] = [
                        'id'                      => (string)$product->id . '_' . $variant['id'],
                        'item_group_id'           => (string)$product->id,
                        'title'                   => $variantTitle,
                        'description'             => $description,
                        'link'                    => $variantLink,
                        'image_link'              => $variantImage,
                        'additional_images'       => array_slice($additionalImages, 0, 5),
                        'availability'            => $availability,
                        'price'                   => $formattedPrice,
                        'sale_price'              => $formattedSalePrice,
                        'brand'                   => $brand,
                        'condition'               => 'new',
                        'color'                   => $variant['color_name'],
                        'size'                    => $variant['size_name'],
                        'product_type'            => $categoryPath,
                        'google_product_category' => $googleCategory,
                    ];
                }
            } else {
                // SINGLE PRODUCT:
                // Uses main image as per specification
                $basePrice = (float)($product->new_price ?? 0);
                $origPrice = ($product->old_price && $product->old_price > $basePrice) ? (float)$product->old_price : $basePrice;
                $salePrice = ($origPrice > $basePrice) ? $basePrice : null;

                $formattedPrice = number_format($origPrice, 2, '.', '') . ' ' . $currency;
                $formattedSalePrice = $salePrice ? (number_format($salePrice, 2, '.', '') . ' ' . $currency) : null;

                $availability = ($product->stock > 0) ? 'in stock' : 'out of stock';

                $feedItems[] = [
                    'id'                      => (string)$product->id,
                    'item_group_id'           => null,
                    'title'                   => $product->name,
                    'description'             => $description,
                    'link'                    => $canonicalLink,
                    'image_link'              => $mainImageUrl,
                    'additional_images'       => array_slice($additionalImages, 0, 5),
                    'availability'            => $availability,
                    'price'                   => $formattedPrice,
                    'sale_price'              => $formattedSalePrice,
                    'brand'                   => $brand,
                    'condition'               => 'new',
                    'color'                   => null,
                    'size'                    => null,
                    'product_type'            => $categoryPath,
                    'google_product_category' => $googleCategory,
                ];
            }
        }

        return $feedItems;
    }

    /**
     * Resolve Main Image for Product (Single products & fallback for variants)
     */
    public function getMainImageUrl($product, ?string $baseUrl = null): string
    {
        $path = null;

        // 1. Checked primary image relation
        if (!empty($product->image?->image)) {
            $path = $product->image->image;
        }

        // 2. Default images without color or size
        if (empty($path) && $product->images && $product->images->isNotEmpty()) {
            $defaultImg = $product->images->first(function ($img) {
                return empty($img->color_id) && empty($img->size_id);
            });

            if ($defaultImg && !empty($defaultImg->image)) {
                $path = $defaultImg->image;
            } else {
                $path = $product->images->first()->image;
            }
        }

        return $this->formatUrl($path, $baseUrl);
    }

    /**
     * Resolve Variant Image:
     * - Looks for image matching variant's color_id and/or size_id
     * - If not found, falls back to main image as instructed
     */
    public function getVariantImageUrl($product, ?int $colorId, ?int $sizeId, string $mainImageUrl, ?string $baseUrl = null): string
    {
        if (!$product->relationLoaded('images')) {
            $product->load('images');
        }

        $matchedImage = null;

        // Priority 1: Match both color and size
        if ($colorId && $sizeId) {
            $matchedImage = $product->images->first(function ($img) use ($colorId, $sizeId) {
                return (int)$img->color_id === (int)$colorId && (int)$img->size_id === (int)$sizeId;
            });
        }

        // Priority 2: Match color (most variation images are assigned per color)
        if (!$matchedImage && $colorId) {
            $matchedImage = $product->images->first(function ($img) use ($colorId) {
                return (int)$img->color_id === (int)$colorId;
            });
        }

        // Priority 3: Match size
        if (!$matchedImage && $sizeId) {
            $matchedImage = $product->images->first(function ($img) use ($sizeId) {
                return (int)$img->size_id === (int)$sizeId;
            });
        }

        // If matched image found with valid path, return it
        if ($matchedImage && !empty($matchedImage->image)) {
            return $this->formatUrl($matchedImage->image, $baseUrl);
        }

        // Fallback to main image
        return $mainImageUrl;
    }

    /**
     * Extract product variants from variantPrices or colors/sizes
     */
    protected function extractProductVariants($product): array
    {
        $variants = [];

        // Check variantPrices relation
        if ($product->variantPrices && $product->variantPrices->isNotEmpty()) {
            foreach ($product->variantPrices as $vp) {
                $colorName = $vp->color ? ($vp->color->name ?? $vp->color->colorName) : null;
                $sizeName = $vp->size ? ($vp->size->name ?? $vp->size->sizeName) : null;

                $variants[] = [
                    'id'         => $vp->id,
                    'color_id'   => $vp->color_id ? (int)$vp->color_id : null,
                    'size_id'    => $vp->size_id ? (int)$vp->size_id : null,
                    'color_name' => $colorName,
                    'size_name'  => $sizeName,
                    'price'      => (float)($vp->price > 0 ? $vp->price : $product->new_price),
                    'stock'      => $vp->stock,
                ];
            }
            return $variants;
        }

        // Fallback: If colors or sizes exist in product relationship
        $hasColors = $product->colors && $product->colors->isNotEmpty();
        $hasSizes = $product->sizes && $product->sizes->isNotEmpty();

        if ($hasColors || $hasSizes) {
            $colors = $hasColors ? $product->colors : collect([null]);
            $sizes = $hasSizes ? $product->sizes : collect([null]);

            $index = 1;
            foreach ($colors as $c) {
                foreach ($sizes as $s) {
                    if ($c === null && $s === null) continue;

                    $colorName = $c ? ($c->name ?? $c->colorName) : null;
                    $sizeName = $s ? ($s->name ?? $s->sizeName) : null;

                    $variants[] = [
                        'id'         => 'c' . ($c?->id ?? 0) . '_s' . ($s?->id ?? 0),
                        'color_id'   => $c?->id ? (int)$c->id : null,
                        'size_id'    => $s?->id ? (int)$s->id : null,
                        'color_name' => $colorName,
                        'size_name'  => $sizeName,
                        'price'      => (float)($product->new_price ?? 0),
                        'stock'      => $product->stock,
                    ];
                }
            }
        }

        return $variants;
    }

    /**
     * Get additional gallery images with dynamic domain
     */
    protected function getAdditionalImages($product, string $mainImageUrl, ?string $baseUrl = null): array
    {
        $additional = [];
        if (!$product->images || $product->images->isEmpty()) {
            return $additional;
        }

        foreach ($product->images as $img) {
            if (empty($img->image)) continue;
            $url = $this->formatUrl($img->image, $baseUrl);
            if ($url !== $mainImageUrl && !in_array($url, $additional)) {
                $additional[] = $url;
            }
        }

        return $additional;
    }

    /**
     * Clean product description for feed XML/CSV
     */
    protected function cleanDescription(?string $desc): string
    {
        if (empty($desc)) {
            return 'Quality product from our collection.';
        }

        $clean = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = strip_tags($clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = trim($clean);

        if (mb_strlen($clean) > 4900) {
            $clean = mb_substr($clean, 0, 4900) . '...';
        }

        return $clean ?: 'Quality product from our collection.';
    }

    /**
     * Build category hierarchy path
     */
    protected function getCategoryPath($product): string
    {
        $parts = [];
        if (!empty($product->category?->name)) {
            $parts[] = $product->category->name;
        }
        if (!empty($product->subcategory?->subcategoryName)) {
            $parts[] = $product->subcategory->subcategoryName;
        }
        if (!empty($product->childcategory?->childcategoryName)) {
            $parts[] = $product->childcategory->childcategoryName;
        }

        return !empty($parts) ? implode(' > ', $parts) : ($product->category?->name ?? 'General');
    }

    /**
     * Format file path into absolute URL with dynamic domain
     */
    public function formatUrl(?string $path, ?string $baseUrl = null): string
    {
        $baseUrl = $baseUrl ?: $this->getBaseUrl();

        if (empty($path)) {
            return $baseUrl . '/public/uploads/default.webp';
        }

        // If it's already an absolute URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // If the URL points to a local uploads/storage path (e.g. from previous domain or localhost in DB),
            // replace it with current dynamic base URL
            $parsed = parse_url($path);
            $parsedPath = $parsed['path'] ?? '';
            if (str_contains($parsedPath, '/uploads/') || str_contains($parsedPath, '/storage/')) {
                return $baseUrl . '/' . ltrim($parsedPath, '/');
            }
            return $path;
        }

        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Wrap string in XML CDATA if needed
     */
    protected function cdata(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Prevent breaking CDATA tag if it contains ]]>
        $safeValue = str_replace(']]>', ']]&gt;', (string)$value);
        return '<![CDATA[' . $safeValue . ']]>';
    }

    /**
     * Resolve store currency code
     */
    protected function getCurrency(?GeneralSetting $setting): string
    {
        if ($setting && !empty($setting->currency)) {
            $curr = strtoupper(trim($setting->currency));
            if (strlen($curr) === 3) {
                return $curr;
            }
        }
        return 'BDT';
    }
}
