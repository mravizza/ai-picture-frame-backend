<?php

namespace App;

class Csrf
{
    /**
     * Get (or generate) the CSRF token for the current session.
     */
    public static function getToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a submitted CSRF token. Sends 403 on failure.
     */
    public static function validate(?string $token): void
    {
        if (!$token || !hash_equals(self::getToken(), $token)) {
            http_response_code(403);
            echo '<h1>403 - Ungueltige Anfrage (CSRF)</h1>';
            exit;
        }
    }
}
