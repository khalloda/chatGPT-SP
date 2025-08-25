<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Purpose: Lightweight PSR-4 autoloader (no Composer) with case-insensitive fallback.
 * Inputs: Root base dir (project/app)
 * Outputs: Class file includes
 */
final class Autoloader
{
    private static string $baseDir;

    public static function register(string $baseDir): void
    {
        self::$baseDir = rtrim($baseDir, '/\\');
        spl_autoload_register([self::class, 'load']);
    }

    private static function load(string $class): void
    {
        if (!str_starts_with($class, 'App\\')) return;

        $relative = str_replace('\\', '/', $class) . '.php';
        $file = self::$baseDir . '/' . $relative;

        if (is_file($file)) { require $file; return; }

        // Portable fallback for case-insensitive filesystems (Windows) vs case-sensitive (Linux)
        $lower = self::$baseDir . '/' . strtolower($relative);
        if (is_file($lower)) { require $lower; return; }
    }
}

