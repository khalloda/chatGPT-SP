<?php
declare(strict_types=1);

namespace App\Config;

/**
 * Purpose: Minimal .env loader (no Composer) suitable for shared hosting.
 * Inputs: .env file at project root
 * Outputs: internal static kv store; getters
 */
final class Env
{
    private static array $data = [];

    public static function load(string $path): void
    {
        if (!is_file($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if ($k !== '') {
                self::$data[$k] = $v;
            }
        }
    }

    public static function get(string $key, mixed $default=null): mixed
    {
        return self::$data[$key] ?? $default;
    }
}

