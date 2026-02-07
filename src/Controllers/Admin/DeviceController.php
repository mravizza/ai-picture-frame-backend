<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;
use App\Models\Device;

class DeviceController
{
    public static function index(): void
    {
        Auth::requireAdmin();
        $devices = Device::findAll();
        $pageTitle = 'Geraete';
        $template = 'devices/index';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function create(): void
    {
        Auth::requireAdmin();
        $device = [
            'device_id' => '',
            'name' => '',
            'slideshow_interval_sec' => 10,
            'face_stable_sec' => 4,
            'cooldown_sec' => 300,
            'similarity_threshold' => 0.650,
            'tts_enabled' => 1,
            'aktiv' => 1,
        ];
        $isEdit = false;
        $pageTitle = 'Geraet erstellen';
        $template = 'devices/form';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function store(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $data = self::validateInput();
        if ($data === null) {
            return;
        }

        $data['api_token'] = Device::generateToken();
        Device::create($data);
        $_SESSION['flash'] = 'Geraet erstellt. API-Token: ' . $data['api_token'];
        redirect('/admin/devices');
    }

    public static function edit(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $device = Device::findById($id);
        if (!$device) {
            redirect('/admin/devices');
        }

        $isEdit = true;
        $pageTitle = 'Geraet bearbeiten';
        $template = 'devices/form';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function update(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $id = (int) ($_POST['id'] ?? 0);
        $device = Device::findById($id);
        if (!$device) {
            redirect('/admin/devices');
        }

        $data = self::validateInput($id);
        if ($data === null) {
            return;
        }

        Device::update($id, $data);
        $_SESSION['flash'] = 'Geraet aktualisiert.';
        redirect('/admin/devices');
    }

    public static function delete(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $id = (int) ($_POST['id'] ?? 0);
        Device::delete($id);
        $_SESSION['flash'] = 'Geraet geloescht.';
        redirect('/admin/devices');
    }

    private static function validateInput(?int $editId = null): ?array
    {
        $deviceId = trim($_POST['device_id'] ?? '');
        if ($deviceId === '') {
            $error = 'Device-ID ist erforderlich.';
            $device = $_POST;
            $isEdit = $editId !== null;
            $pageTitle = $isEdit ? 'Geraet bearbeiten' : 'Geraet erstellen';
            $template = 'devices/form';
            include TEMPLATE_PATH . '/layout.php';
            return null;
        }

        // Check uniqueness
        $existing = Device::findByDeviceId($deviceId);
        if ($existing && (int) $existing['id'] !== $editId) {
            $error = 'Diese Device-ID existiert bereits.';
            $device = $_POST;
            $isEdit = $editId !== null;
            $pageTitle = $isEdit ? 'Geraet bearbeiten' : 'Geraet erstellen';
            $template = 'devices/form';
            include TEMPLATE_PATH . '/layout.php';
            return null;
        }

        return [
            'device_id'              => $deviceId,
            'name'                   => trim($_POST['name'] ?? ''),
            'slideshow_interval_sec' => max(1, (int) ($_POST['slideshow_interval_sec'] ?? 10)),
            'face_stable_sec'        => max(1, (int) ($_POST['face_stable_sec'] ?? 4)),
            'cooldown_sec'           => max(0, (int) ($_POST['cooldown_sec'] ?? 300)),
            'similarity_threshold'   => max(0, min(1, (float) ($_POST['similarity_threshold'] ?? 0.65))),
            'tts_enabled'            => isset($_POST['tts_enabled']),
            'aktiv'                  => isset($_POST['aktiv']),
        ];
    }
}
