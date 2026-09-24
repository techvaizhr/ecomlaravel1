<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Productimage;
use App\Support\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImportService
{
    /**
     * Browser User-Agents pool to prevent scraping blocks.
     */
    protected static array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0',
    ];

    /**
     * Fetch and parse product data from any supported or generic URL.
     */
    public static function fetch(string $url): array
    {
        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'অনুগ্রহ করে একটি সঠিক প্রোডাক্ট ইউআরএল (Valid URL) প্রদান করুন।'];
        }

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        $ua = self::$userAgents[array_rand(self::$userAgents)];

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'User-Agent'      => $ua,
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9,bn;q=0.8',
                'Cache-Control'   => 'no-cache',
                'Pragma'          => 'no-cache',
            ])->timeout(25)->get($url);

            if (!$response->successful() && $response->status() !== 200) {
                return [
                    'success' => false,
                    'message' => "প্রোডাক্ট পেজ লোড করা যায়নি (HTTP Status: {$response->status()})। লিঙ্কটি পাবলিকলি এক্সেসযোগ্য কিনা যাচাই করুন।"
                ];
            }

            $html = $response->body();
            if (empty($html) || strlen($html) < 200) {
                return ['success' => false, 'message' => 'পেজের কন্টেন্ট খালি বা অ্যাক্সেস ব্লক করা হয়েছে।'];
            }

            // Platform-specific parsers
            if (str_contains($host, 'daraz.') || str_contains($host, 'lazada.')) {
                $parsed = self::parseDaraz($html, $url);
            } elseif (str_contains($host, 'alibaba.com')) {
                $parsed = self::parseAlibaba($html, $url);
            } elseif (str_contains($host, 'aliexpress.')) {
                $parsed = self::parseAliExpress($html, $url);
            } elseif (str_contains($host, 'amazon.')) {
                $parsed = self::parseAmazon($html, $url);
            } elseif (str_contains($host, 'ebay.')) {
                $parsed = self::parseEbay($html, $url);
            } else {
                $parsed = self::parseGeneric($html, $url);
            }

            // Common Fallbacks & Polish
            $parsed = self::applySmartFallbacks($parsed, $html, $url);

            // Match suggested Category & Brand from local DB
            $parsed['suggested_category_id'] = self::findMatchingCategory($parsed['name'], $parsed['description']);
            $parsed['brand_id'] = self::findMatchingBrand($parsed['brand'] ?? '', $parsed['name']);

            return [
                'success' => true,
                'data'    => $parsed
            ];
        } catch (\Throwable $e) {
            Log::error('ProductImportService fetch error: ' . $e->getMessage(), ['url' => $url]);
            return [
                'success' => false,
                'message' => 'প্রোডাক্ট তথ্য সংগ্রহ করতে সমস্যা হয়েছে: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse raw HTML content directly (useful for Alibaba / captcha-protected sites when pasted from browser).
     */
    public static function parseHtmlContent(string $html, string $sourceUrl = ''): array
    {
        $html = trim($html);
        if (empty($html) || strlen($html) < 100) {
            return ['success' => false, 'message' => 'পেস্ট করা HTML কন্টেন্ট খালি বা অসম্পূর্ণ।'];
        }

        $sourceUrl = trim($sourceUrl);
        $host = '';
        if (!empty($sourceUrl)) {
            $host = strtolower(parse_url($sourceUrl, PHP_URL_HOST) ?? '');
        }

        // Auto-detect platform from HTML content if host not provided
        if (empty($host)) {
            if (str_contains($html, 'alibaba.com') || str_contains($html, 'alicdn.com')) {
                $host = 'alibaba.com';
            } elseif (str_contains($html, 'daraz.com') || str_contains($html, 'daraz-pwa')) {
                $host = 'daraz.com.bd';
            } elseif (str_contains($html, 'aliexpress.com')) {
                $host = 'aliexpress.com';
            } elseif (str_contains($html, 'amazon.com') || str_contains($html, 'media-amazon.com')) {
                $host = 'amazon.com';
            }
        }

        if (str_contains($host, 'daraz.') || str_contains($host, 'lazada.')) {
            $parsed = self::parseDaraz($html, $sourceUrl);
        } elseif (str_contains($host, 'alibaba.com')) {
            $parsed = self::parseAlibaba($html, $sourceUrl);
        } elseif (str_contains($host, 'aliexpress.')) {
            $parsed = self::parseAliExpress($html, $sourceUrl);
        } elseif (str_contains($host, 'amazon.')) {
            $parsed = self::parseAmazon($html, $sourceUrl);
        } elseif (str_contains($host, 'ebay.')) {
            $parsed = self::parseEbay($html, $sourceUrl);
        } else {
            $parsed = self::parseGeneric($html, $sourceUrl);
        }

        $parsed = self::applySmartFallbacks($parsed, $html, $sourceUrl);
        $parsed['suggested_category_id'] = self::findMatchingCategory($parsed['name'], $parsed['description']);
        $parsed['brand_id'] = self::findMatchingBrand($parsed['brand'] ?? '', $parsed['name']);

        return [
            'success' => true,
            'data'    => $parsed
        ];
    }

    /**
     * PARSER: DARAZ & LAZADA
     */
    protected static function parseDaraz(string $html, string $url): array
    {
        $result = [
            'name'            => '',
            'new_price'       => 0,
            'old_price'       => 0,
            'purchase_price'  => 0,
            'description'     => '',
            'images'          => [],
            'sku'             => '',
            'brand'           => '',
            'source_platform' => 'Daraz',
            'source_url'      => $url,
        ];

        // 1. JSON extraction from __moduleData__
        if (preg_match('/var\s+__moduleData__\s*=\s*(\{.+?\});\s*(?:var|window|app|\n)/s', $html, $m)) {
            $data = json_decode($m[1], true);
            if ($data && isset($data['data']['root']['fields'])) {
                $fields = $data['data']['root']['fields'];

                // Title
                if (!empty($fields['product']['title'])) {
                    $result['name'] = trim((string) $fields['product']['title']);
                }

                // Prices
                if (!empty($fields['skuInfos'])) {
                    foreach ($fields['skuInfos'] as $sku) {
                        if (!empty($sku['price'])) {
                            $sp = $sku['price']['salePrice']['value'] ?? 0;
                            $op = $sku['price']['originalPrice']['value'] ?? 0;
                            if ($sp > 0) {
                                $result['new_price'] = (float) $sp;
                                $result['old_price'] = $op > 0 ? (float) $op : (float) $sp;
                                break;
                            }
                        }
                    }
                }

                // Gallery Images
                if (!empty($fields['skuGalleries'])) {
                    foreach ($fields['skuGalleries'] as $gallery) {
                        if (is_array($gallery)) {
                            foreach ($gallery as $imgItem) {
                                $src = $imgItem['src'] ?? '';
                                if ($src) {
                                    $result['images'][] = self::cleanImageUrl($src);
                                }
                            }
                        }
                    }
                }

                // Description
                $descHtml = '';
                if (!empty($fields['product']['desc'])) {
                    $descHtml = $fields['product']['desc'];
                }
                if (!empty($fields['product']['highlights']) && is_array($fields['product']['highlights'])) {
                    $hlHtml = '<h5>Key Highlights:</h5><ul>';
                    foreach ($fields['product']['highlights'] as $hl) {
                        $hlHtml .= '<li>' . strip_tags($hl) . '</li>';
                    }
                    $hlHtml .= '</ul><br>';
                    $descHtml = $hlHtml . $descHtml;
                }
                $result['description'] = $descHtml;

                // Brand & SKU
                if (!empty($fields['specifications'])) {
                    foreach ($fields['specifications'] as $spec) {
                        if (!empty($spec['features']['Brand'])) {
                            $result['brand'] = (string) $spec['features']['Brand'];
                        }
                        if (!empty($spec['features']['SKU'])) {
                            $result['sku'] = (string) $spec['features']['SKU'];
                        }
                    }
                }
            }
        }

        // 2. Fallback price from pdt_price in tracking data
        if (empty($result['new_price']) || $result['new_price'] <= 0) {
            if (preg_match('/"pdt_price":\s*"[^"\d]*([\d\.,]+)"/i', $html, $pm)) {
                $p = (float) str_replace(',', '', $pm[1]);
                if ($p > 0) {
                    $result['new_price'] = $p;
                    $result['old_price'] = round($p * 1.25);
                    $result['purchase_price'] = round($p * 0.7);
                }
            }
        }

        // 3. Fallback from schema.org JSON-LD in Daraz HTML
        if (preg_match_all('/<script[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/is', $html, $ldMatches)) {
            foreach ($ldMatches[1] as $jsonStr) {
                $ld = json_decode(trim($jsonStr), true);
                if ($ld && ($ld['@type'] ?? '') === 'Product') {
                    if (empty($result['name']) && !empty($ld['name'])) {
                        $result['name'] = trim((string) $ld['name']);
                    }
                    if (empty($result['images']) && !empty($ld['image'])) {
                        $imgs = is_array($ld['image']) ? $ld['image'] : [$ld['image']];
                        foreach ($imgs as $img) {
                            $result['images'][] = self::cleanImageUrl($img);
                        }
                    }
                    if (empty($result['brand']) && !empty($ld['brand']['name'])) {
                        $result['brand'] = (string) $ld['brand']['name'];
                    }
                    if (empty($result['sku']) && !empty($ld['sku'])) {
                        $result['sku'] = (string) $ld['sku'];
                    }
                    if (empty($result['description']) && !empty($ld['description'])) {
                        $result['description'] = '<p>' . nl2br(e($ld['description'])) . '</p>';
                    }
                }
            }
        }

        return $result;
    }

    /**
     * PARSER: ALIBABA
     */
    protected static function parseAlibaba(string $html, string $url): array
    {
        $result = [
            'name'            => '',
            'new_price'       => 0,
            'old_price'       => 0,
            'purchase_price'  => 0,
            'description'     => '',
            'images'          => [],
            'sku'             => '',
            'brand'           => '',
            'source_platform' => 'Alibaba',
            'source_url'      => $url,
        ];

        // 1. Title Extraction
        // 1a. From JSON payload
        if (preg_match('/"subject":\s*"([^"]+)"/i', $html, $m)) {
            $result['name'] = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        } elseif (preg_match('/"productSubject":\s*"([^"]+)"/i', $html, $m)) {
            $result['name'] = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        } elseif (preg_match('/"productTitle":\s*"([^"]+)"/i', $html, $m)) {
            $result['name'] = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // 1b. From H1 tags
        if (empty($result['name'])) {
            if (preg_match('/<h1[^>]*class="[^"]*product-title[^"]*"[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                $result['name'] = trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            } elseif (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                $cleanH1 = trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                if (strlen($cleanH1) > 10 && !stripos($cleanH1, 'alibaba')) {
                    $result['name'] = $cleanH1;
                }
            }
        }

        // 1c. From Meta OG / Twitter / Title tag
        if (empty($result['name'])) {
            if (preg_match('/<meta\s+(?:property="og:title"|name="twitter:title")\s+content="([^"]+)"/i', $html, $m)) {
                $t = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $result['name'] = trim(preg_replace('/\s*-\s*Buy\s+.*Alibaba\.com.*$/i', '', $t));
            } elseif (preg_match('/<title>([^<]+)<\/title>/i', $html, $m)) {
                $t = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $result['name'] = trim(preg_replace('/\s*-\s*Buy\s+.*Alibaba\.com.*$/i', '', $t));
            }
        }

        // Clean any residual Alibaba brand suffix
        if (!empty($result['name'])) {
            $result['name'] = trim(preg_replace('/\s*-\s*Alibaba\.com.*$/i', '', $result['name']));
            $result['name'] = trim(preg_replace('/\s*\|\s*Alibaba\.com.*$/i', '', $result['name']));
            $result['name'] = trim(preg_replace('/\s*on\s+Alibaba\.com.*$/i', '', $result['name']));
        }

        // 1d. Fallback title from URL slug
        if (empty($result['name']) && preg_match('/\/product-detail\/([^_]+)[_-](\d+)\.html/i', $url, $m)) {
            $slugTitle = str_replace(['-', '_'], ' ', $m[1]);
            $result['name'] = ucwords(trim($slugTitle));
            $result['sku'] = 'ALIBABA-' . $m[2];
        }

        // 2. Prices & Tiers
        $usdRate = 120; // 1 USD ~ 120 BDT approx baseline
        if (preg_match_all('/data-testid="pc-purchase-price-tier"[^>]*>.*?data-testid="pc-purchase-price-tier-current">([^<]+)<\/strong>.*?<div[^>]*>([^<]+)<\/div>/s', $html, $m, PREG_SET_ORDER)) {
            $tiers = [];
            foreach ($m as $tier) {
                $rawP = preg_replace('/[^\d\.]/', '', $tier[1]);
                $priceNum = (float) $rawP;
                $tiers[] = ['price' => $priceNum, 'qty' => trim($tier[2])];
            }
            if (!empty($tiers)) {
                $baseUsd = $tiers[0]['price'];
                $result['new_price'] = round($baseUsd * $usdRate);
                $result['old_price'] = round($result['new_price'] * 1.3);
                $result['purchase_price'] = round($baseUsd * $usdRate * 0.8);
            }
        } elseif (preg_match('/"formattedPrice":\s*"([^"]+)"/i', $html, $m)) {
            $usd = (float) preg_replace('/[^\d\.]/', '', $m[1]);
            if ($usd > 0) {
                $result['new_price'] = round($usd * $usdRate);
                $result['old_price'] = round($result['new_price'] * 1.25);
                $result['purchase_price'] = round($result['new_price'] * 0.75);
            }
        } elseif (preg_match('/"minPrice":\s*([\d\.]+)/i', $html, $m)) {
            $usd = (float) $m[1];
            if ($usd > 0) {
                $result['new_price'] = round($usd * $usdRate);
                $result['old_price'] = round($result['new_price'] * 1.25);
                $result['purchase_price'] = round($result['new_price'] * 0.75);
            }
        } elseif (preg_match('/"ladderPriceList":\s*(\[\{.+?\}\])/s', $html, $m)) {
            $ladder = json_decode($m[1], true);
            if (is_array($ladder) && !empty($ladder[0]['price'])) {
                $usd = (float) preg_replace('/[^\d\.]/', '', (string) $ladder[0]['price']);
                if ($usd > 0) {
                    $result['new_price'] = round($usd * $usdRate);
                    $result['old_price'] = round($result['new_price'] * 1.25);
                    $result['purchase_price'] = round($result['new_price'] * 0.75);
                }
            }
        } elseif (preg_match('/data-price-product="([^"]+)"/i', $html, $m)) {
            $usd = (float) preg_replace('/[^\d\.]/', '', $m[1]);
            if ($usd > 0) {
                $result['new_price'] = round($usd * $usdRate);
                $result['old_price'] = round($result['new_price'] * 1.25);
                $result['purchase_price'] = round($result['new_price'] * 0.75);
            }
        }

        // 3. Images from HTML & JSON
        // 3a. window.detailData imagePathList
        if (preg_match('/"imagePathList":\s*(\[[^\]]+\])/i', $html, $m)) {
            $paths = json_decode($m[1], true);
            if (is_array($paths)) {
                foreach ($paths as $p) {
                    if (str_starts_with($p, '//')) $p = 'https:' . $p;
                    $result['images'][] = self::cleanImageUrl($p);
                }
            }
        }

        // 3b. All alicdn product images ending in .jpg/.png/.webp
        if (preg_match_all('/https?:\/\/[a-zA-Z0-9_.\/-]*alicdn\.com\/kf\/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)/i', $html, $m)) {
            foreach ($m[0] as $img) {
                // Filter out non-product assets
                if (preg_match('/(express|super_buyer|flags|avatar|shield|badge|logo|icon)/i', $img)) {
                    continue;
                }
                $result['images'][] = self::cleanImageUrl($img);
            }
        }

        // 3c. Main image thumbnails
        if (preg_match_all('/(?:data-testid="main-image-thumbnail"[^>]*style="background-image:url\(([^)]+)\)|src="([^"]+alicdn\.com\/@sc04\/kf\/[^"]+)")/i', $html, $m)) {
            $all = array_filter(array_merge($m[1], $m[2]));
            foreach ($all as $img) {
                $img = trim($img, '\'"');
                if (str_starts_with($img, '//')) $img = 'https:' . $img;
                $result['images'][] = self::cleanImageUrl($img);
            }
        }

        // 3d. Additional JSON imageUrls
        if (preg_match_all('/"imageUrl":\s*"([^"]+alicdn\.com[^"]+)"/i', $html, $m)) {
            foreach ($m[1] as $img) {
                $img = stripslashes($img);
                if (str_starts_with($img, '//')) $img = 'https:' . $img;
                $result['images'][] = self::cleanImageUrl($img);
            }
        }

        $result['images'] = array_values(array_unique(array_filter($result['images'])));

        // 4. Attributes / Description table
        if (preg_match('/data-testid="three-column-key-attributes">(.*?)<\/div><\/div>/s', $html, $m)) {
            $specHtml = '<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th colspan="2">Specifications / Key Attributes</th></tr></thead><tbody>';
            if (preg_match_all('/title="([^"]+)"[^>]*>\s*<span[^>]*>([^<]+)<\/span>/i', $m[1], $attMatches, PREG_SET_ORDER)) {
                foreach ($attMatches as $att) {
                    $k = trim($att[1]);
                    $v = trim($att[2]);
                    if ($k && $v) {
                        $specHtml .= '<tr><td style="width:35%;font-weight:600;">' . e($k) . '</td><td>' . e($v) . '</td></tr>';
                    }
                }
            }
            $specHtml .= '</tbody></table></div>';
            $result['description'] = $specHtml;
        } elseif (preg_match('/<div[^>]*class="[^"]*(?:product-attribute|do-entry-list|key-attributes)[^"]*"[^>]*>(.*?)<\/div>/is', $html, $m)) {
            $result['description'] = '<div class="product-attributes">' . $m[1] . '</div>';
        }

        return $result;
    }

    /**
     * PARSER: ALIEXPRESS
     */
    protected static function parseAliExpress(string $html, string $url): array
    {
        $result = [
            'name'            => '',
            'new_price'       => 0,
            'old_price'       => 0,
            'purchase_price'  => 0,
            'description'     => '',
            'images'          => [],
            'sku'             => '',
            'brand'           => '',
            'source_platform' => 'AliExpress',
            'source_url'      => $url,
        ];

        if (preg_match('/window\.runParams\s*=\s*(\{.+?\});\s*(?:window|var|<\/script>)/s', $html, $m)) {
            $data = json_decode($m[1], true);
            if ($data) {
                $pData = $data['data'] ?? $data;
                $result['name'] = $pData['titleModule']['subject'] ?? '';
                if (!empty($pData['priceModule']['formatedActivityPrice'])) {
                    $result['new_price'] = (float) preg_replace('/[^\d\.]/', '', $pData['priceModule']['formatedActivityPrice']);
                }
                if (!empty($pData['priceModule']['formatedPrice'])) {
                    $result['old_price'] = (float) preg_replace('/[^\d\.]/', '', $pData['priceModule']['formatedPrice']);
                }
                if (!empty($pData['imageModule']['imagePathList'])) {
                    foreach ($pData['imageModule']['imagePathList'] as $img) {
                        $result['images'][] = self::cleanImageUrl($img);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * PARSER: AMAZON
     */
    protected static function parseAmazon(string $html, string $url): array
    {
        $result = [
            'name'            => '',
            'new_price'       => 0,
            'old_price'       => 0,
            'purchase_price'  => 0,
            'description'     => '',
            'images'          => [],
            'sku'             => '',
            'brand'           => '',
            'source_platform' => 'Amazon',
            'source_url'      => $url,
        ];

        // Title
        if (preg_match('/<span\s+id="productTitle"[^>]*>([^<]+)<\/span>/i', $html, $m)) {
            $result['name'] = trim(html_entity_decode($m[1]));
        }

        // Price
        if (preg_match('/<span\s+class="a-offscreen">([^<]+)<\/span>/i', $html, $m)) {
            $p = (float) preg_replace('/[^\d\.]/', '', $m[1]);
            $result['new_price'] = round($p * 120); // USD -> BDT standard multiplier
            $result['old_price'] = round($result['new_price'] * 1.25);
        }

        // Images from colorImages
        if (preg_match('/\'colorImages\':\s*\{\s*\'initial\':\s*(\[\{.+?\}\])/s', $html, $m)) {
            $imgData = json_decode($m[1], true);
            if ($imgData) {
                foreach ($imgData as $item) {
                    if (!empty($item['hiRes'])) {
                        $result['images'][] = $item['hiRes'];
                    } elseif (!empty($item['large'])) {
                        $result['images'][] = $item['large'];
                    }
                }
            }
        }

        // Description / Bullets
        if (preg_match('/<div\s+id="feature-bullets"[^>]*>(.*?)<\/div>/s', $html, $m)) {
            if (preg_match_all('/<span\s+class="a-list-item"[^>]*>([^<]+)<\/span>/i', $m[1], $bm)) {
                $desc = '<ul>';
                foreach ($bm[1] as $bullet) {
                    $bullet = trim(html_entity_decode($bullet));
                    if ($bullet) $desc .= '<li>' . e($bullet) . '</li>';
                }
                $desc .= '</ul>';
                $result['description'] = $desc;
            }
        }

        return $result;
    }

    /**
     * PARSER: EBAY
     */
    protected static function parseEbay(string $html, string $url): array
    {
        $result = [
            'name'            => '',
            'new_price'       => 0,
            'old_price'       => 0,
            'purchase_price'  => 0,
            'description'     => '',
            'images'          => [],
            'sku'             => '',
            'brand'           => '',
            'source_platform' => 'eBay',
            'source_url'      => $url,
        ];

        if (preg_match('/<h1[^>]*class="[^"]*x-item-title__mainTitle[^"]*"[^>]*>\s*<span[^>]*>([^<]+)<\/span>/i', $html, $m)) {
            $result['name'] = trim(html_entity_decode($m[1]));
        }

        if (preg_match('/itemprop="price"[^>]*content="([\d\.]+)"/i', $html, $m)) {
            $p = (float) $m[1];
            $result['new_price'] = round($p * 120);
            $result['old_price'] = round($result['new_price'] * 1.2);
        }

        return $result;
    }

    /**
     * PARSER: GENERIC / SCHEMA.ORG / OPENGRAPH (Universal Fallback)
     */
    protected static function parseGeneric(string $html, string $url): array
    {
        $result = [
            'name'            => '',
            'new_price'       => 0,
            'old_price'       => 0,
            'purchase_price'  => 0,
            'description'     => '',
            'images'          => [],
            'sku'             => '',
            'brand'           => '',
            'source_platform' => parse_url($url, PHP_URL_HOST) ?: 'Web',
            'source_url'      => $url,
        ];

        // 1. Schema.org JSON-LD
        if (preg_match_all('/<script\s+type="application\/ld\+json"[^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $data = json_decode(trim($jsonStr), true);
                if (!$data) continue;

                // Handle @graph or nested items
                $items = isset($data['@graph']) && is_array($data['@graph']) ? $data['@graph'] : [$data];
                foreach ($items as $item) {
                    $type = $item['@type'] ?? '';
                    if ($type === 'Product' || (is_array($type) && in_array('Product', $type))) {
                        $result['name'] = $item['name'] ?? $result['name'];
                        $result['description'] = $item['description'] ?? $result['description'];
                        $result['sku'] = $item['sku'] ?? $result['sku'];

                        if (isset($item['brand']['name'])) {
                            $result['brand'] = $item['brand']['name'];
                        }

                        // Images
                        if (!empty($item['image'])) {
                            $imgArr = is_array($item['image']) ? $item['image'] : [$item['image']];
                            foreach ($imgArr as $img) {
                                if (is_string($img)) {
                                    $result['images'][] = self::cleanImageUrl($img);
                                } elseif (isset($img['url'])) {
                                    $result['images'][] = self::cleanImageUrl($img['url']);
                                }
                            }
                        }

                        // Price
                        if (!empty($item['offers'])) {
                            $offers = isset($item['offers'][0]) ? $item['offers'][0] : $item['offers'];
                            $p = (float) ($offers['price'] ?? $offers['lowPrice'] ?? 0);
                            if ($p > 0) {
                                $result['new_price'] = $p;
                                if (!empty($offers['highPrice'])) {
                                    $result['old_price'] = (float) $offers['highPrice'];
                                }
                            }
                        }
                        break 2;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Apply OpenGraph & Meta fallbacks to ensure zero missing fields.
     */
    protected static function applySmartFallbacks(array $parsed, string $html, string $url): array
    {
        // 1. Name fallback
        if (empty($parsed['name'])) {
            if (preg_match('/<meta\s+property="og:title"\s+content="([^"]+)"/i', $html, $m)) {
                $parsed['name'] = trim(html_entity_decode($m[1]));
            } elseif (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $m)) {
                $parsed['name'] = trim(html_entity_decode($m[1]));
            }
        }

        // Clean name
        $parsed['name'] = preg_replace('/(\s*\|\s*.*|\s*-\s*Buy\s+.*|\s*-\s*Daraz.*|\s*:\s*Amazon\..*)$/i', '', $parsed['name']);
        $parsed['name'] = trim($parsed['name']);

        // 2. Images fallback
        if (empty($parsed['images'])) {
            if (preg_match('/<meta\s+property="og:image"\s+content="([^"]+)"/i', $html, $m)) {
                $parsed['images'][] = self::cleanImageUrl($m[1]);
            }
            if (preg_match_all('/<meta\s+property="og:image:secure_url"\s+content="([^"]+)"/i', $html, $m)) {
                foreach ($m[1] as $u) {
                    $parsed['images'][] = self::cleanImageUrl($u);
                }
            }
            // Preload image tag
            if (preg_match('/<link\s+rel="preload"\s+as="image"\s+href="([^"]+)"/i', $html, $m)) {
                $parsed['images'][] = self::cleanImageUrl($m[1]);
            }
        }

        // Deduplicate and filter valid HTTP images
        $cleanedImages = [];
        foreach ($parsed['images'] as $img) {
            $img = self::cleanImageUrl($img);
            if (filter_var($img, FILTER_VALIDATE_URL) && !in_array($img, $cleanedImages)) {
                $cleanedImages[] = $img;
            }
        }
        $parsed['images'] = array_values($cleanedImages);

        // 3. Price Fallback
        if (empty($parsed['new_price']) || $parsed['new_price'] <= 0) {
            if (preg_match('/<meta\s+property="product:price:amount"\s+content="([\d\.]+)"/i', $html, $m)) {
                $parsed['new_price'] = (float) $m[1];
            } elseif (preg_match('/<meta\s+property="og:price:amount"\s+content="([\d\.]+)"/i', $html, $m)) {
                $parsed['new_price'] = (float) $m[1];
            }
        }

        if (empty($parsed['old_price']) || $parsed['old_price'] <= $parsed['new_price']) {
            if ($parsed['new_price'] > 0) {
                // Default old price 15-20% higher for attractive discount badge
                $parsed['old_price'] = round($parsed['new_price'] * 1.2);
            }
        }

        if (empty($parsed['purchase_price']) || $parsed['purchase_price'] <= 0) {
            if ($parsed['new_price'] > 0) {
                // Default purchase cost 70% of sale price
                $parsed['purchase_price'] = round($parsed['new_price'] * 0.7);
            }
        }

        // 4. Description fallback
        if (empty($parsed['description'])) {
            if (preg_match('/<meta\s+property="og:description"\s+content="([^"]+)"/i', $html, $m)) {
                $parsed['description'] = '<p>' . nl2br(e(html_entity_decode($m[1]))) . '</p>';
            } elseif (preg_match('/<meta\s+name="description"\s+content="([^"]+)"/i', $html, $m)) {
                $parsed['description'] = '<p>' . nl2br(e(html_entity_decode($m[1]))) . '</p>';
            } else {
                $parsed['description'] = '<p>' . e($parsed['name']) . '</p>';
            }
        }

        // 5. Meta tags & SEO
        $parsed['meta_title'] = Str::limit($parsed['name'], 65, '');
        $parsed['meta_description'] = Str::limit(strip_tags($parsed['description']), 155, '...');
        $parsed['meta_keywords'] = implode(', ', array_slice(array_filter(explode(' ', preg_replace('/[^\w\s]/u', '', $parsed['name']))), 0, 7));

        // 6. Defaults
        $parsed['stock'] = 100;
        $parsed['pro_unit'] = 'pcs';
        $parsed['product_type'] = 'physical';
        $parsed['status'] = 1;

        return $parsed;
    }

    /**
     * Clean and normalize image URLs to get high-resolution originals.
     */
    protected static function cleanImageUrl(string $url): string
    {
        $url = trim($url, '\'" ');
        if (str_starts_with($url, '//')) {
            $url = 'https:' . $url;
        }

        // Remove query parameters
        $clean = preg_replace('/\?.*/', '', $url);

        // Remove trailing _.webp
        $clean = preg_replace('/_\.webp$/i', '', $clean);

        // Remove Daraz / Lazada / Alibaba resize suffixes (e.g. _720x720q80.jpg, _350x350.jpg, _Q90.jpg)
        $clean = preg_replace('/_(?:\d+x\d+|Q\d+)[^.]*\.(jpg|jpeg|png|webp)$/i', '', $clean);

        // If the URL ended up without a file extension, restore .jpg
        if (!preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $clean)) {
            $clean .= '.jpg';
        }

        return $clean ?: $url;
    }

    /**
     * Match product text against existing Categories in database.
     */
    protected static function findMatchingCategory(string $title, string $desc): ?int
    {
        try {
            $categories = Category::where('status', 1)->select('id', 'name')->get();
            $text = strtolower($title . ' ' . strip_tags($desc));

            foreach ($categories as $cat) {
                $catName = strtolower(trim($cat->name));
                if (strlen($catName) > 2 && str_contains($text, $catName)) {
                    return $cat->id;
                }
            }

            // Fallback: Return first available category if exists
            return $categories->first()?->id;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Match product brand against existing Brands in database.
     */
    protected static function findMatchingBrand(string $scrapedBrand, string $title): ?int
    {
        try {
            $brands = Brand::where('status', 1)->select('id', 'name')->get();
            $checkStr = strtolower($scrapedBrand . ' ' . $title);

            foreach ($brands as $brand) {
                $bName = strtolower(trim($brand->name));
                if (strlen($bName) > 1 && str_contains($checkStr, $bName)) {
                    return $brand->id;
                }
            }
        } catch (\Throwable) {}

        return null;
    }

    /**
     * Download remote image URLs, optimize them to WebP, and attach to Product.
     */
    public static function downloadAndAttachImages(array $imageUrls, Product $product, int $maxImages = 8): array
    {
        $savedImages = [];
        $imageUrls = array_slice(array_unique(array_filter($imageUrls)), 0, $maxImages);

        foreach ($imageUrls as $idx => $url) {
            try {
                $resp = Http::withoutVerifying()->withHeaders([
                    'User-Agent' => self::$userAgents[0],
                ])->timeout(15)->get($url);

                if (!$resp->successful()) continue;

                $binary = $resp->body();
                if (strlen($binary) < 500) continue; // too small to be a real product image

                // Create temp file
                $tempPath = tempnam(sys_get_temp_dir(), 'imp_img_');
                file_put_contents($tempPath, $binary);

                $mime = @mime_content_type($tempPath) ?: 'image/jpeg';
                $name = basename(parse_url($url, PHP_URL_PATH)) ?: 'imported-' . ($idx + 1) . '.jpg';

                $uploadedFile = new UploadedFile(
                    $tempPath,
                    $name,
                    $mime,
                    null,
                    true // test mode
                );

                $savedPath = ImageOptimizer::storeProductImage($uploadedFile);
                @unlink($tempPath);

                if ($savedPath) {
                    Productimage::create([
                        'product_id' => $product->id,
                        'image'      => $savedPath,
                    ]);
                    $savedImages[] = $savedPath;

                    // If product has no meta image yet, assign first image
                    if (empty($product->meta_image)) {
                        $product->update(['meta_image' => $savedPath]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to download imported product image ($url): " . $e->getMessage());
                if (isset($tempPath) && file_exists($tempPath)) {
                    @unlink($tempPath);
                }
            }
        }

        return $savedImages;
    }
}
