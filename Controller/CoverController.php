<?php

require_once __DIR__ . '/BaseController.php';

/**
 * CoverController
 *
 * Resolves book cover image URLs via the hosted BookCover API and redirects the browser (302)
 * to the real image URL so it can be used directly in <img src="...">.
 */
class CoverController extends BaseController
{
    /**
     * Redirect to the resolved cover image URL by ISBN.
     *
     * Route: /book_store/?page=cover&isbn=9780345376596
     *
     * Success: 302 redirect to the real image URL.
     * Failure: 302 redirect to local placeholder.
     */
    public function redirectByIsbn(string $isbn): void
    {
        $placeholder = '/book_store/Content/images/books/no-image.jpg';

        $cacheTtlSeconds = defined('COVER_METADATA_CACHE_TTL')
            ? max(0, (int)constant('COVER_METADATA_CACHE_TTL'))
            : 86400; // 1 day

        $negativeCacheTtlSeconds = defined('COVER_METADATA_NEGATIVE_TTL')
            ? max(0, (int)constant('COVER_METADATA_NEGATIVE_TTL'))
            : 300; // 5 minutes

        $isbn = trim((string)$isbn);
        if ($isbn === '') {
            $this->redirect($placeholder, 302);
        }

        // Normalize ISBN (digits + X)
        $isbnClean = preg_replace('/[^0-9Xx]/', '', $isbn);
        if ($isbnClean === '') {
            $this->redirect($placeholder, 302);
        }

        // Hosted BookCover API endpoint per docs
        $baseUrl = defined('BOOKCOVER_API_BASE_URL')
            ? rtrim(constant('BOOKCOVER_API_BASE_URL'), '/')
            : 'https://bookcover.longitood.com';

        $apiUrl = $baseUrl . '/bookcover/' . rawurlencode($isbnClean);

        // Basic file cache: avoid resolving the same ISBN repeatedly.
        // (Helps a lot on pages with many covers like /?page=books.)
        $cacheDir = __DIR__ . '/../tmp/cover-cache';
        $cacheKey = sha1($apiUrl);
        $cacheFile = $cacheDir . '/' . $cacheKey . '.json';

        $resolved = null;

        if (is_file($cacheFile)) {
            $ageSeconds = time() - (int)@filemtime($cacheFile);
            $cached = @file_get_contents($cacheFile);
            if (is_string($cached) && $cached !== '') {
                $cachedData = json_decode($cached, true);

                // Positive cache: keep for 1 day (or configured TTL)
                if ($cacheTtlSeconds > 0 && $ageSeconds < $cacheTtlSeconds) {
                    if (is_array($cachedData) && isset($cachedData['url']) && is_string($cachedData['url'])) {
                        $resolved = trim($cachedData['url']);
                    }
                }

                // Negative cache: avoid hammering the API for short period
                if ((!is_string($resolved) || $resolved === '') && $negativeCacheTtlSeconds > 0 && $ageSeconds < $negativeCacheTtlSeconds) {
                    $isMiss = is_array($cachedData) && (($cachedData['status'] ?? '') === 'miss');
                    if ($isMiss) {
                        header('Cache-Control: public, max-age=' . $negativeCacheTtlSeconds);
                        $this->redirect($placeholder, 302);
                    }
                }
            }
        }

        if (!is_string($resolved) || $resolved === '') {
            $resolved = $this->resolveImageUrlFromApi($apiUrl);
            if (is_string($resolved) && $resolved !== '') {
                if (!is_dir($cacheDir)) {
                    @mkdir($cacheDir, 0777, true);
                }
                @file_put_contents(
                    $cacheFile,
                    json_encode(['url' => $resolved, 'cached_at' => time()], JSON_UNESCAPED_SLASHES),
                    LOCK_EX
                );
            }
        }

        if (!is_string($resolved) || $resolved === '') {
            // Cache placeholder briefly (negative cache) to reduce repeated failing requests.
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0777, true);
            }
            @file_put_contents(
                $cacheFile,
                json_encode(['status' => 'miss', 'cached_at' => time()], JSON_UNESCAPED_SLASHES),
                LOCK_EX
            );
            header('Cache-Control: public, max-age=' . $negativeCacheTtlSeconds);
            $this->redirect($placeholder, 302);
        }

        // Basic safety: only allow http(s)
        if (!preg_match('#^https?://#i', $resolved)) {
            $this->redirect($placeholder, 302);
        }

        // Cache successful resolves for a day.
        header('Cache-Control: public, max-age=' . $cacheTtlSeconds);
        $this->redirect($resolved, 302);
    }

    /**
     * Fetch JSON from BookCover API and extract the image URL.
     * Expected payload: {"url": "https://..."}
     */
    private function resolveImageUrlFromApi(string $apiUrl): ?string
    {
        // Use cURL if available (common in XAMPP). Fallback to file_get_contents.
        $json = null;

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                // Keep timeouts short so slow API/network doesn't make navigation feel "stuck".
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                ],
            ]);

            $json = curl_exec($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    // Keep timeouts short so slow API/network doesn't make navigation feel "stuck".
                    'timeout' => 2,
                    'header' => "Accept: application/json\r\n",
                ],
            ]);

            $json = @file_get_contents($apiUrl, false, $context);
        }

        if (!is_string($json) || $json === '') {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }

        $url = $data['url'] ?? null;
        return is_string($url) ? trim($url) : null;
    }
}
