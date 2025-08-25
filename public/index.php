<?php
declare(strict_types=1);
/**
 * Purpose: Single entry point. Boots env, sessions, autoloader, router.
 * Outputs: HTTP response
 * Dependencies: app/bootstrap.php
 */
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;

// Define routes with minimal public health route to verify deployment
$router = new Router();

// Public health-check (no auth)
$router->get('/health', fn() => print 'OK');

// Auth routes (controller included in this part)
$router->get('/', [\App\Controllers\AuthController::class, 'loginForm']);
$router->get('/login', [\App\Controllers\AuthController::class, 'loginForm']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Example protected route (controller delivered in later parts)
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'])
       ->middleware(['auth']);

// Dispatch
$router->dispatch();
