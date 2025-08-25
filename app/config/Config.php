<?php
declare(strict_types=1);

namespace App\Config;

/**
 * Purpose: Central config assembled from Env with sane defaults.
 * Outputs: date/timezone, debug, app name/url, mail settings
 */
final class Config
{
    public static function init(): void
    {
        date_default_timezone_set(Env::get('TIMEZONE', 'Africa/Cairo'));

        $debug = Env::get('APP_DEBUG', 'false') === 'true';
        if ($debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', '0');
        }
    }

    public static function app(string $key, mixed $default=null): mixed
    {
        $map = [
            'name' => Env::get('APP_NAME', 'Spare Parts Management'),
            'url'  => Env::get('APP_URL', 'http://localhost'),
            'debug'=> Env::get('APP_DEBUG', 'false') === 'true',
        ];
        return $map[$key] ?? $default;
    }
}

