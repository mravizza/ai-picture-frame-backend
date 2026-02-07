<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;
use App\Models\Photo;
use App\Models\Person;
use App\Models\PersonPhoto;

class PhotoController
{
    public static function index(): void
    {
        Auth::requireAdmin();

        $personId = isset($_GET['person_id']) ? (int) $_GET['person_id'] : null;
        $photos = Photo::findAll($personId);
        $persons = Person::findAll();
        $filterPersonId = $personId;

        // Attach person names to each photo for display
        foreach ($photos as &$photo) {
            $photo['persons'] = Photo::getPersons((int) $photo['id']);
        }
        unset($photo);

        $pageTitle = 'Fotos';
        $template = 'photos/index';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function showUpload(): void
    {
        Auth::requireAdmin();
        $persons = Person::findAll();
        $pageTitle = 'Fotos hochladen';
        $template = 'photos/upload';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function doUpload(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $uploadDir = config('upload_dir');
        $allowedMimes = config('allowed_mimes');
        $maxSize = config('max_upload_mb') * 1024 * 1024;
        $personIds = $_POST['person_ids'] ?? [];

        if (empty($_FILES['photos']['name'][0])) {
            $_SESSION['flash_error'] = 'Keine Dateien ausgewaehlt.';
            redirect('/admin/photos/upload');
        }

        $uploaded = 0;
        $errors = [];
        $files = $_FILES['photos'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = $files['name'][$i] . ': Upload-Fehler.';
                continue;
            }

            $tmpPath = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            $size = $files['size'][$i];
            $mime = mime_content_type($tmpPath);

            if (!in_array($mime, $allowedMimes, true)) {
                $errors[] = $originalName . ': Ungültiger Dateityp (' . $mime . ').';
                continue;
            }

            if ($size > $maxSize) {
                $errors[] = $originalName . ': Datei zu gross (max ' . config('max_upload_mb') . ' MB).';
                continue;
            }

            $checksum = hash_file('sha256', $tmpPath);

            // Skip duplicates
            $existing = Photo::findByChecksum($checksum);
            if ($existing) {
                $errors[] = $originalName . ': Duplikat (bereits vorhanden).';
                continue;
            }

            // Generate unique filename
            $ext = self::getExtension($mime);
            $filename = uuid_v4() . '.' . $ext;
            $destPath = $uploadDir . '/' . $filename;

            if (!move_uploaded_file($tmpPath, $destPath)) {
                $errors[] = $originalName . ': Speichern fehlgeschlagen.';
                continue;
            }

            $photoId = Photo::create([
                'filename'          => $filename,
                'original_filename' => $originalName,
                'mime'              => $mime,
                'checksum'          => $checksum,
                'file_size'         => $size,
            ]);

            // Assign to persons
            foreach ($personIds as $pid) {
                PersonPhoto::assign((int) $pid, $photoId);
            }

            $uploaded++;
        }

        $msg = $uploaded . ' Foto(s) hochgeladen.';
        if ($errors) {
            $msg .= ' Fehler: ' . implode(' ', $errors);
        }
        $_SESSION['flash'] = $msg;
        redirect('/admin/photos');
    }

    public static function showAssign(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $photo = Photo::findById($id);
        if (!$photo) {
            redirect('/admin/photos');
        }

        $persons = Person::findAll();
        $assignedIds = PersonPhoto::getPersonIdsForPhoto($id);
        $pageTitle = 'Foto zuordnen';
        $template = 'photos/assign';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function doAssign(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $photo = Photo::findById($photoId);
        if (!$photo) {
            redirect('/admin/photos');
        }

        $personIds = $_POST['person_ids'] ?? [];
        PersonPhoto::syncForPhoto($photoId, $personIds);

        $_SESSION['flash'] = 'Zuordnung gespeichert.';
        redirect('/admin/photos');
    }

    public static function delete(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $id = (int) ($_POST['id'] ?? 0);
        Photo::delete($id);
        $_SESSION['flash'] = 'Foto geloescht.';
        redirect('/admin/photos');
    }

    private static function getExtension(string $mime): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        return $map[$mime] ?? 'jpg';
    }
}
