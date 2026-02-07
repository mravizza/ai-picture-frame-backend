<?php

namespace App\Models;

use App\Database;

class Log
{
    public static function findAll(array $filters = [], int $limit = 50): array
    {
        $pdo = Database::getConnection();
        $params = [];
        $where = [];

        $sql = 'SELECT l.*, p.vorname AS person_vorname FROM logs l LEFT JOIN persons p ON p.id = l.person_id';

        if (!empty($filters['device_id'])) {
            $where[] = 'l.device_id = ?';
            $params[] = $filters['device_id'];
        }
        if (!empty($filters['person_id'])) {
            $where[] = 'l.person_id = ?';
            $params[] = (int) $filters['person_id'];
        }
        if (!empty($filters['type'])) {
            $where[] = 'l.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['from'])) {
            $where[] = 'l.created_at >= ?';
            $params[] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = 'l.created_at <= ?';
            $params[] = $filters['to'] . ' 23:59:59';
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY l.created_at DESC LIMIT ' . $limit;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT l.*, p.vorname AS person_vorname FROM logs l LEFT JOIN persons p ON p.id = l.person_id WHERE l.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO logs (device_id, person_id, type, message, payload) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['device_id'],
            $data['person_id'] ?? null,
            $data['type'],
            $data['message'] ?? null,
            $data['payload'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function getTypes(): array
    {
        $pdo = Database::getConnection();
        return array_column(
            $pdo->query('SELECT DISTINCT type FROM logs ORDER BY type')->fetchAll(),
            'type'
        );
    }

    public static function getDeviceIds(): array
    {
        $pdo = Database::getConnection();
        return array_column(
            $pdo->query('SELECT DISTINCT device_id FROM logs ORDER BY device_id')->fetchAll(),
            'device_id'
        );
    }

    public static function countRecent(int $hours = 24): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)');
        $stmt->execute([$hours]);
        return (int) $stmt->fetchColumn();
    }
}
