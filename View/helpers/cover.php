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
    $placeholder = BASE_URL . 'Content/images/books/no-image.jpg';

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
