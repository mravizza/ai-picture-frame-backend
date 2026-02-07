<?php

namespace App\Models;

use App\Database;

class PersonPhoto
{
    public static function assign(int $personId, int $photoId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO person_photo (person_id, photo_id) VALUES (?, ?)'
        );
        return $stmt->execute([$personId, $photoId]);
    }

    public static function unassign(int $personId, int $photoId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'DELETE FROM person_photo WHERE person_id = ? AND photo_id = ?'
        );
        return $stmt->execute([$personId, $photoId]);
    }

    /**
     * Sync the assignments for a photo: set exactly the given person IDs.
     */
    public static function syncForPhoto(int $photoId, array $personIds): void
    {
        $pdo = Database::getConnection();

        // Remove existing
        $stmt = $pdo->prepare('DELETE FROM person_photo WHERE photo_id = ?');
        $stmt->execute([$photoId]);

        // Insert new
        $stmt = $pdo->prepare('INSERT INTO person_photo (person_id, photo_id) VALUES (?, ?)');
        foreach ($personIds as $personId) {
            $stmt->execute([(int) $personId, $photoId]);
        }
    }

    /**
     * Get assigned person IDs for a photo.
     */
    public static function getPersonIdsForPhoto(int $photoId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT person_id FROM person_photo WHERE photo_id = ?');
        $stmt->execute([$photoId]);
        return array_column($stmt->fetchAll(), 'person_id');
    }
}
