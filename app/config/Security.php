<?php
declare(strict_types=1);

namespace App\Config;

/**
 * Purpose: Add global security headers early in lifecycle.
 * Outputs: Security headers
 */
final class Security
{
    public static function applyHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}

