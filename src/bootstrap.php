<?php

/**
 * Bootstrap: autoloader, config, session, constants.
 * Required by public/index.php on every request.
 */

// Paths
define('ROOT_PATH', realpath(__DIR__ . '/..'));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');
define('TEMPLATE_PATH', ROOT_PATH . '/templates');

// Load config
$appConfig = require CONFIG_PATH . '/config.php';
$localConfig = require CONFIG_PATH . '/config.local.php';
$config = array_merge($appConfig, $localConfig);

// Make config globally accessible
$GLOBALS['config'] = $config;

// Timezone
date_default_timezone_set($config['timezone']);

// Error reporting (disable display in production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Session configuration
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}

// Start session only for non-API requests
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($requestUri, '/api/') === false) {
    session_start();
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// PSR-4-like autoloader for App\ namespace -> src/
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $file = SRC_PATH . DIRECTORY_SEPARATOR . $relative . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load helper functions
require SRC_PATH . '/helpers.php';
