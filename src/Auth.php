<?php

namespace App;

use App\Models\AdminUser;
use App\Models\Device;

class Auth
{
    /**
     * Require admin session. Redirects to login if not authenticated.
     */
    public static function requireAdmin(): void
    {
        if (empty($_SESSION['admin_user_id'])) {
            redirect('/admin/login');
        }
    }

    /**
     * Check if an admin is currently logged in.
     */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['admin_user_id']);
    }

    /**
     * Get the current admin username.
     */
    public static function adminUsername(): ?string
    {
        return $_SESSION['admin_username'] ?? null;
    }

    /**
     * Log in an admin user. Returns true on success.
     */
    public static function loginAdmin(string $username, string $password): bool
    {
        $user = AdminUser::findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        return true;
    }

    /**
     * Log out the current admin.
     */
    public static function logoutAdmin(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Require a valid device API token. Returns device row or sends 401.
     */
    public static function requireDevice(): array
    {
        $token = self::getBearerToken();
        if (!$token) {
            json_response(['error' => 'Unauthorized: missing Bearer token'], 401);
        }

        $device = Device::findByToken($token);
        if (!$device) {
            json_response(['error' => 'Unauthorized: invalid token'], 401);
        }

        if (!$device['aktiv']) {
            json_response(['error' => 'Unauthorized: device inactive'], 403);
        }

        // Update last seen
        Device::updateLastSeen($device['id']);

        return $device;
    }

    /**
     * Extract Bearer token from Authorization header.
     */
    private static function getBearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
