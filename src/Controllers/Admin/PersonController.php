<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;
use App\Models\Person;

class PersonController
{
    public static function index(): void
    {
        Auth::requireAdmin();
        $persons = Person::findAll();
        $pageTitle = 'Personen';
        $template = 'persons/index';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function create(): void
    {
        Auth::requireAdmin();
        $person = [
            'vorname' => '',
            'rolle_beziehung' => '',
            'bevorzugte_anrede' => '',
            'greeting_text' => '',
            'themen' => '',
            'gespraechslaenge' => 'mittel',
            'aktiv' => 1,
        ];
        $isEdit = false;
        $pageTitle = 'Person erstellen';
        $template = 'persons/form';
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

        Person::create($data);
        $_SESSION['flash'] = 'Person erstellt.';
        redirect('/admin/persons');
    }

    public static function edit(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $person = Person::findById($id);
        if (!$person) {
            redirect('/admin/persons');
        }

        $isEdit = true;
        $pageTitle = 'Person bearbeiten';
        $template = 'persons/form';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function update(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $id = (int) ($_POST['id'] ?? 0);
        $person = Person::findById($id);
        if (!$person) {
            redirect('/admin/persons');
        }

        $data = self::validateInput();
        if ($data === null) {
            return;
        }

        Person::update($id, $data);
        $_SESSION['flash'] = 'Person aktualisiert.';
        redirect('/admin/persons');
    }

    public static function delete(): void
    {
        Auth::requireAdmin();
        Csrf::validate($_POST['_csrf'] ?? null);

        $id = (int) ($_POST['id'] ?? 0);
        Person::delete($id);
        $_SESSION['flash'] = 'Person geloescht.';
        redirect('/admin/persons');
    }

    private static function validateInput(): ?array
    {
        $vorname = trim($_POST['vorname'] ?? '');
        if ($vorname === '') {
            $error = 'Vorname ist erforderlich.';
            $person = $_POST;
            $isEdit = !empty($_POST['id']);
            $pageTitle = $isEdit ? 'Person bearbeiten' : 'Person erstellen';
            $template = 'persons/form';
            include TEMPLATE_PATH . '/layout.php';
            return null;
        }

        $allowed = ['kurz', 'mittel', 'lang'];
        $gespraechslaenge = $_POST['gespraechslaenge'] ?? 'mittel';
        if (!in_array($gespraechslaenge, $allowed, true)) {
            $gespraechslaenge = 'mittel';
        }

        return [
            'vorname'            => $vorname,
            'rolle_beziehung'    => trim($_POST['rolle_beziehung'] ?? ''),
            'bevorzugte_anrede'  => trim($_POST['bevorzugte_anrede'] ?? ''),
            'greeting_text'      => trim($_POST['greeting_text'] ?? ''),
            'themen'             => trim($_POST['themen'] ?? ''),
            'gespraechslaenge'   => $gespraechslaenge,
            'aktiv'              => isset($_POST['aktiv']),
        ];
    }
}
