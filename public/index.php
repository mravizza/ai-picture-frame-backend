<?php

/**
 * Front Controller – Single entry point for all requests.
 */

require __DIR__ . '/../src/bootstrap.php';

use App\Router;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\PersonController;
use App\Controllers\Admin\PhotoController;
use App\Controllers\Admin\DeviceController;
use App\Controllers\Admin\LogController as AdminLogController;
use App\Controllers\Api\ConfigController as ApiConfigController;
use App\Controllers\Api\PersonController as ApiPersonController;
use App\Controllers\Api\PhotoController as ApiPhotoController;
use App\Controllers\Api\LogController as ApiLogController;

// ── Serve uploaded files ──────────────────────────────────────
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Strip base path prefix (e.g. /public) so /public/uploads/... also matches
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
$cleanPath = $basePath && strpos($requestPath, $basePath) === 0
    ? substr($requestPath, strlen($basePath)) ?: '/'
    : $requestPath;
if (preg_match('#^/uploads/([a-f0-9\-]+\.\w{3,4})$#', $cleanPath, $m)) {
    $filePath = ROOT_PATH . '/uploads/' . $m[1];
    if (is_file($filePath)) {
        $mime = mime_content_type($filePath);
        if (in_array($mime, config('allowed_mimes', []), true)) {
            // Clean output buffers to avoid memory issues with large files
            while (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: public, max-age=86400');
            readfile($filePath);
            exit;
        }
    }
    http_response_code(404);
    exit;
}

$router = new Router();

// ── Admin: Auth ──────────────────────────────────────────────
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'doLogin']);
$router->get('/admin/logout', [AuthController::class, 'logout']);

// ── Admin: Dashboard ─────────────────────────────────────────
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/dashboard', [DashboardController::class, 'index']);

// ── Admin: Persons ───────────────────────────────────────────
$router->get('/admin/persons', [PersonController::class, 'index']);
$router->get('/admin/persons/create', [PersonController::class, 'create']);
$router->post('/admin/persons/store', [PersonController::class, 'store']);
$router->get('/admin/persons/edit', [PersonController::class, 'edit']);
$router->post('/admin/persons/update', [PersonController::class, 'update']);
$router->post('/admin/persons/delete', [PersonController::class, 'delete']);

// ── Admin: Photos ────────────────────────────────────────────
$router->get('/admin/photos', [PhotoController::class, 'index']);
$router->get('/admin/photos/upload', [PhotoController::class, 'showUpload']);
$router->post('/admin/photos/upload', [PhotoController::class, 'doUpload']);
$router->get('/admin/photos/assign', [PhotoController::class, 'showAssign']);
$router->post('/admin/photos/assign', [PhotoController::class, 'doAssign']);
$router->post('/admin/photos/delete', [PhotoController::class, 'delete']);

// ── Admin: Devices ───────────────────────────────────────────
$router->get('/admin/devices', [DeviceController::class, 'index']);
$router->get('/admin/devices/create', [DeviceController::class, 'create']);
$router->post('/admin/devices/store', [DeviceController::class, 'store']);
$router->get('/admin/devices/edit', [DeviceController::class, 'edit']);
$router->post('/admin/devices/update', [DeviceController::class, 'update']);
$router->post('/admin/devices/delete', [DeviceController::class, 'delete']);

// ── Admin: Logs ──────────────────────────────────────────────
$router->get('/admin/logs', [AdminLogController::class, 'index']);
$router->get('/admin/logs/detail', [AdminLogController::class, 'detail']);

// ── API v1 ───────────────────────────────────────────────────
$router->get('/api/v1/config', [ApiConfigController::class, 'index']);
$router->get('/api/v1/persons', [ApiPersonController::class, 'index']);
$router->get('/api/v1/photos', [ApiPhotoController::class, 'index']);
$router->post('/api/v1/logs/event', [ApiLogController::class, 'store']);

// ── Root redirect ────────────────────────────────────────────
$router->get('/', [DashboardController::class, 'index']);

// Dispatch
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
