<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Helpers;
use App\Core\I18n;

/**
 * Route table:
 * GET  /login  → loginForm
 * POST /login  → login
 * GET  /logout → logout
 */
final class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) { $this->redirect('/dashboard'); }
        $this->view('auth/login');
    }

    public function login(): void
    {
        if (!Helpers::verifyCsrf()) {
            $this->setFlash('error', 'Invalid request.');
            $this->redirect('/login');
        }

        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // NOTE: Real auth logic (User model) provided in later parts.
        // For bootability, accept a fixed admin email during first run:
        if (strcasecmp($data['email'], 'admin@example.com') === 0 && $data['password'] === 'Admin@123') {
            $_SESSION['user_id'] = 1;
            Helpers::clearOldInputAndErrors();
            $this->setFlash('success', I18n::t('auth.login_success'));
            $this->redirect('/dashboard');
        }

        $this->setFlash('error', I18n::t('auth.login_failed'));
        $this->redirect('/login');
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
        $this->setFlash('success', I18n::t('auth.logout_success'));
        $this->redirect('/login');
    }
}

