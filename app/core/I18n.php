<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Purpose: Minimal i18n loader, EN/AR arrays.
 */
final class I18n
{
    private static string $locale = 'en';
    private static array $translations = [];

    public static function init(string $locale, array $supported, string $dir): void
    {
        self::$locale = in_array($locale, $supported, true) ? $locale : 'en';
        $file = rtrim($dir, '/\\') . '/' . self::$locale . '.php';
        if (is_file($file)) {
            self::$translations = require $file;
        }
    }

    public static function t(string $key, array $vars=[]): string
    {
        $text = self::$translations[$key] ?? $key;
        foreach ($vars as $k=>$v) $text = str_replace('{' . $k . '}', (string)$v, $text);
        return $text;
    }
}

