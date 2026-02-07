<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;

class AuthController
{
    public static function showLogin(): void
    {
        if (Auth::isLoggedIn()) {
            redirect('/admin/dashboard');
        }

        $error = null;
        $template = 'auth/login';
        $pageTitle = 'Login';
        $hideNav = true;
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function doLogin(): void
    {
        Csrf::validate($_POST['_csrf'] ?? null);

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Benutzername und Passwort erforderlich.';
            $template = 'auth/login';
            $pageTitle = 'Login';
            $hideNav = true;
            include TEMPLATE_PATH . '/layout.php';
            return;
        }

        if (Auth::loginAdmin($username, $password)) {
            redirect('/admin/dashboard');
        }

        $error = 'Ungueltige Anmeldedaten.';
        $template = 'auth/login';
        $pageTitle = 'Login';
        $hideNav = true;
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function logout(): void
    {
        Auth::logoutAdmin();
        redirect('/admin/login');
    }
}
