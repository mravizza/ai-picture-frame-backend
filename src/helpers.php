<?php

/**
 * Helper functions available globally.
 */

/**
 * Escape HTML output to prevent XSS.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL and exit.
 */
function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

/**
 * Send a JSON response and exit.
 */
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Build a full URL from a relative path.
 */
function base_url(string $path = ''): string
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $base = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
    return $base . $path;
}

/**
 * Get the app config value.
 */
function config(string $key, $default = null)
{
    return $GLOBALS['config'][$key] ?? $default;
}

/**
 * Get the current CSRF token for forms.
 */
function csrf_field(): string
{
    $token = \App\Csrf::getToken();
    return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
}

/**
 * Get the current request method.
 */
function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

/**
 * Read JSON body from request.
 */
function json_body(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Format a datetime for display.
 */
function format_datetime(?string $datetime): string
{
    if (!$datetime) {
        return '-';
    }
    return date('d.m.Y H:i', strtotime($datetime));
}

/**
 * Generate a UUID v4.
 */
function uuid_v4(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
