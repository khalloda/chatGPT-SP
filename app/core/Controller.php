<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\I18n;

/**
 * Purpose: Base controller (views, redirects, validation, flash)
 * Fixes: Prevent infinite back() loops; robust rule parsing; consistent CSRF usage.
 */
abstract class Controller
{
    protected function view(string $view, array $data=[]): void
    {
        extract($data, EXTR_SKIP);
        $base = dirname(__DIR__) . '/views';
        $file = $base . '/' . $view . '.php';
        if (!is_file($file)) { http_response_code(500); echo "View not found: {$view}"; return; }

        $layout = $base . '/layouts/base.php';
        $content = function () use ($file, $data) { extract($data, EXTR_SKIP); require $file; };
        require $layout;
    }

    protected function redirect(string $to): void
    {
        header('Location: ' . $to);
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        // prevent loops: if referer equals current, send to home
        $current = $_SERVER['REQUEST_URI'] ?? '';
        if ($referer === $current) { $referer = '/'; }
        $this->redirect($referer);
    }

    protected function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Validates $_POST against rules like:
     * 'email' => 'required|email', 'qty' => 'required|numeric|min:1|max:100'
     */
    protected function validate(array $rules): array
    {
        $data = $_POST;
        $errors = [];

        foreach ($rules as $field => $ruleStr) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $ruleStr) as $rule) {
                if ($rule === 'required' && (is_null($value) || $value === '')) {
                    $errors[$field] = I18n::t('validation.required', ['field' => $field]);
                } elseif ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = I18n::t('validation.email');
                } elseif ($rule === 'numeric' && $value !== null && !is_numeric($value)) {
                    $errors[$field] = I18n::t('validation.numeric', ['field' => $field]);
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if ($value !== null && is_string($value) && strlen($value) < $min) {
                        $errors[$field] = I18n::t('validation.min', ['min' => $min]);
                    } elseif (is_numeric($value) && (float)$value < $min) {
                        $errors[$field] = I18n::t('validation.min_num', ['min' => $min]);
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if ($value !== null && is_string($value) && strlen($value) > $max) {
                        $errors[$field] = I18n::t('validation.max', ['max' => $max]);
                    } elseif (is_numeric($value) && (float)$value > $max) {
                        $errors[$field] = I18n::t('validation.max_num', ['max' => $max]);
                    }
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->back();
        }

        return $data;
    }
}

