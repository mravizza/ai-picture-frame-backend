<?php

namespace App\Models;

use App\Database;

class Device
{
    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query('SELECT * FROM devices ORDER BY device_id ASC')->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM devices WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByDeviceId(string $deviceId): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM devices WHERE device_id = ? LIMIT 1');
        $stmt->execute([$deviceId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByToken(string $token): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM devices WHERE api_token = ? LIMIT 1');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO devices (device_id, api_token, name, slideshow_interval_sec, face_stable_sec, cooldown_sec, similarity_threshold, tts_enabled, aktiv)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['device_id'],
            $data['api_token'],
            $data['name'] ?: null,
            (int) ($data['slideshow_interval_sec'] ?? 10),
            (int) ($data['face_stable_sec'] ?? 4),
            (int) ($data['cooldown_sec'] ?? 300),
            (float) ($data['similarity_threshold'] ?? 0.65),
            isset($data['tts_enabled']) ? 1 : 0,
            isset($data['aktiv']) ? 1 : 0,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE devices SET device_id = ?, name = ?, slideshow_interval_sec = ?, face_stable_sec = ?, cooldown_sec = ?, similarity_threshold = ?, tts_enabled = ?, aktiv = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $data['device_id'],
            $data['name'] ?: null,
            (int) ($data['slideshow_interval_sec'] ?? 10),
            (int) ($data['face_stable_sec'] ?? 4),
            (int) ($data['cooldown_sec'] ?? 300),
            (float) ($data['similarity_threshold'] ?? 0.65),
            isset($data['tts_enabled']) ? 1 : 0,
            isset($data['aktiv']) ? 1 : 0,
            $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM devices WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function updateLastSeen(int $id): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE devices SET last_seen_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function countActive(): int
    {
        $pdo = Database::getConnection();
        return (int) $pdo->query('SELECT COUNT(*) FROM devices WHERE aktiv = 1')->fetchColumn();
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
