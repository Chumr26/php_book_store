<?php

/**
 * Cover helper
 *
 * Generates book cover URLs using the BookCover API (https://github.com/w3slley/bookcover-api)
 * based on ISBN, with a safe placeholder fallback.
 */

/**
 * Build a BookCover API URL from ISBN.
 *
 * @param string|null $isbn
 * @param string $size One of: small|medium|large (depends on API deployment)
 * @return string
 */
function book_cover_url(?string $isbn, string $size = 'medium'): string
{
    // Local placeholder (keep this file in your project)
    $placeholder = BASE_URL . 'Content/images/books/no-image.webp';

    $isbn = trim((string)$isbn);
    if ($isbn === '') {
        return $placeholder;
    }

    // Normalize ISBN (keep digits and X)
    $isbn = preg_replace('/[^0-9Xx]/', '', $isbn);
    if ($isbn === '') {
        return $placeholder;
    }

    // IMPORTANT: The hosted BookCover API returns JSON (not an image).
    // So this helper returns a local proxy route that 302-redirects to the real image URL.
    // Route is handled by `CoverController` via: ?page=cover&isbn=...
    return BASE_URL . '?page=cover&isbn=' . rawurlencode($isbn);
}

/**
 * Build local uploaded cover URL from a stored path.
 *
 * @param string|null $path
 * @return string|null
 */
function book_local_cover_url(?string $path): ?string
{
    $path = trim((string)$path);
    if ($path === '') {
        error_log('[cover] empty path');
        return null;
    }

    error_log('[cover] raw path=' . $path);

    if (preg_match('#^https?://#i', $path)) {
        error_log('[cover] external url=' . $path);
        return $path;
    }

    $normalizedPath = str_replace('\\', '/', $path);

    if (preg_match('#(?:^|/)(Content/images/books/)(.+)$#i', $normalizedPath, $m)) {
        $path = $m[1] . $m[2];
    } elseif (preg_match('#(?:^|/)(upload/)(.+)$#i', $normalizedPath, $m)) {
        $path = $m[1] . $m[2];
    } else {
        $path = ltrim($normalizedPath, '/');
    }

    error_log('[cover] normalized path=' . $path);

    if (defined('BASE_PATH')) {
        $absolutePath = BASE_PATH . ltrim($path, '/');
        error_log('[cover] absolute=' . $absolutePath);

        if (is_file($absolutePath)) {
            error_log('[cover] found absolute');
            return BASE_URL . ltrim($path, '/');
        }

        $fallbackPath = 'Content/images/books/' . ltrim(basename($path), '/');
        $fallbackAbsolute = BASE_PATH . $fallbackPath;
        error_log('[cover] fallback absolute=' . $fallbackAbsolute);

        if (is_file($fallbackAbsolute)) {
            error_log('[cover] found fallback');
            return BASE_URL . $fallbackPath;
        }

        error_log('[cover] not found');
        return null;
    }

    return BASE_URL . ltrim($path, '/');
}

/**
 * Resolve cover URL for a book record.
 * Prefer uploaded local image (hinh_anh/anh_bia), fallback to ISBN cover.
 *
 * @param array $book
 * @param string $size
 * @return string
 */
function book_cover_url_for_book(array $book, string $size = 'medium'): string
{
    $localPath = $book['hinh_anh'] ?? ($book['anh_bia'] ?? null);
    $localUrl = book_local_cover_url($localPath);
    if ($localUrl) {
        return $localUrl;
    }

    return book_cover_url($book['isbn'] ?? null, $size);
}
