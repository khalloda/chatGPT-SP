<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Env;

/**
 * Purpose: Common helpers (CSRF, input, escape, currency)
 * Fixes: Add clearOldInputAndErrors(); regenerate CSRF token periodically.
 */
final class Helpers
{
    public static function input(string $key, mixed $default=null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function e(string $html): string
    {
        return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        $ttl = (int) Env::get('CSRF_TTL_SECONDS', '3600');
        $now = time();
        if (!isset($_SESSION['_token']) || !isset($_SESSION['_token_time']) || ($now - (int)$_SESSION['_token_time']) > $ttl) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
            $_SESSION['_token_time'] = $now;
        }
        return $_SESSION['_token'];
    }

    public static function verifyCsrf(): bool
    {
        $token = self::input('_token', '');
        return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
    }

    public static function clearOldInputAndErrors(): void
    {
        unset($_SESSION['errors'], $_SESSION['old']);
    }

    public static function old(string $key, mixed $default=null): mixed
    {
        return $_SESSION['old'][$key] ?? $default;
    }

    public static function flash(string $key): ?string
    {
        if (!isset($_SESSION['flash'][$key])) return null;
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    // Simplified currency formatting (full multicurrency details appear in later parts)
    public static function formatCurrency(float $amount, string $code='USD'): string
    {
        return number_format($amount, 2) . ' ' . $code;
    }

    public static function setUserCurrency(string $code): void
    {
        $_SESSION['currency'] = strtoupper($code);
    }
}

