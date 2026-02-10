<?php

namespace App\Models;

use App\Database;

class Person
{
    public static function findAll(bool $activeOnly = false): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT p.*, (SELECT COUNT(*) FROM person_photo pp WHERE pp.person_id = p.id) AS photo_count FROM persons p';
        if ($activeOnly) {
            $sql .= ' WHERE p.aktiv = 1';
        }
        $sql .= ' ORDER BY p.vorname ASC';
        return $pdo->query($sql)->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM persons WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO persons (vorname, rolle_beziehung, bevorzugte_anrede, greeting_text, themen, gespraechslaenge, sprache, aktiv)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['vorname'],
            $data['rolle_beziehung'] ?: null,
            $data['bevorzugte_anrede'] ?: null,
            $data['greeting_text'] ?: null,
            $data['themen'] ?: null,
            $data['gespraechslaenge'] ?? 'mittel',
            $data['sprache'] ?? 'hochdeutsch',
            isset($data['aktiv']) ? 1 : 0,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE persons SET vorname = ?, rolle_beziehung = ?, bevorzugte_anrede = ?, greeting_text = ?, themen = ?, gespraechslaenge = ?, sprache = ?, aktiv = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $data['vorname'],
            $data['rolle_beziehung'] ?: null,
            $data['bevorzugte_anrede'] ?: null,
            $data['greeting_text'] ?: null,
            $data['themen'] ?: null,
            $data['gespraechslaenge'] ?? 'mittel',
            $data['sprache'] ?? 'hochdeutsch',
            isset($data['aktiv']) ? 1 : 0,
            $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM persons WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function count(bool $activeOnly = false): int
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT COUNT(*) FROM persons';
        if ($activeOnly) {
            $sql .= ' WHERE aktiv = 1';
        }
        return (int) $pdo->query($sql)->fetchColumn();
    }
}
