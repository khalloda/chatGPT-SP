<?php
declare(strict_types=1);
/**
 * Purpose: Central bootstrap for environment, sessions, autoloader, config, i18n.
 * Outputs: Initialized app state
 * Dependencies: Env, Config, Autoloader
 */
ini_set('display_errors', '0'); // final behavior controlled by Config/App_DEBUG

// Autoloader (PSR-4)
require __DIR__ . '/core/Autoloader.php';
\App\Core\Autoloader::register(__DIR__ . '/..');

// Load env first
require __DIR__ . '/config/Env.php';
\App\Config\Env::load(dirname(__DIR__) . '/.env');

// Secure session (IIS-friendly)
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_name(\App\Config\Env::get('SESSION_COOKIE_NAME', 'sp_session'));
session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'domain'   => '',
  'secure'   => $secure,
  'httponly' => true,
  'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Initialize config (timezone, debug)
require __DIR__ . '/config/Config.php';
\App\Config\Config::init();

// Basic i18n boot
require __DIR__ . '/core/I18n.php';
\App\Core\I18n::init(
  \App\Config\Env::get('LOCALE', 'en'),
  explode(',', \App\Config\Env::get('SUPPORTED_LOCALES', 'en,ar')),
  __DIR__ . '/lang'
);

// Core helpers & security utilities
require __DIR__ . '/core/Helpers.php';
require __DIR__ . '/config/Security.php';
\App\Config\Security::applyHeaders();

