<?php

namespace App\Models;

use App\Database;

class Photo
{
    public static function findAll(?int $personId = null, ?string $since = null): array
    {
        $pdo = Database::getConnection();
        $params = [];
        $sql = 'SELECT p.* FROM photos p';
        $where = [];

        if ($personId !== null) {
            $sql .= ' INNER JOIN person_photo pp ON pp.photo_id = p.id';
            $where[] = 'pp.person_id = ?';
            $params[] = $personId;
        }

        if ($since !== null) {
            $where[] = 'p.created_at >= ?';
            $params[] = $since;
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY p.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM photos WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByChecksum(string $checksum): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM photos WHERE checksum = ? LIMIT 1');
        $stmt->execute([$checksum]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO photos (filename, original_filename, mime, checksum, file_size, description)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['filename'],
            $data['original_filename'],
            $data['mime'],
            $data['checksum'],
            $data['file_size'],
            $data['description'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function updateDescription(int $id, ?string $description): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE photos SET description = ? WHERE id = ?');
        return $stmt->execute([$description, $id]);
    }

    public static function delete(int $id): bool
    {
        $photo = self::findById($id);
        if ($photo) {
            $filePath = config('upload_dir') . '/' . $photo['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM photos WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function count(): int
    {
        $pdo = Database::getConnection();
        return (int) $pdo->query('SELECT COUNT(*) FROM photos')->fetchColumn();
    }

    /**
     * Get persons assigned to a photo.
     */
    public static function getPersons(int $photoId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.* FROM persons p
             INNER JOIN person_photo pp ON pp.person_id = p.id
             WHERE pp.photo_id = ?
             ORDER BY p.vorname'
        );
        $stmt->execute([$photoId]);
        return $stmt->fetchAll();
    }
}
