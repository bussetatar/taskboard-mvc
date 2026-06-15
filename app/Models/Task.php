<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Task
{
    public static function allByUser(int $userId, array $filters = []): array
    {
        $sql = 'SELECT tasks.*, categories.name AS category_name
                FROM tasks
                LEFT JOIN categories ON categories.id = tasks.category_id
                WHERE tasks.user_id = :user_id';
        $parameters = ['user_id' => $userId];

        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'completed'], true)) {
            $sql .= ' AND tasks.status = :status';
            $parameters['status'] = $filters['status'];
        }

        if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
            $sql .= ' AND tasks.category_id = :category_id';
            $parameters['category_id'] = (int) $filters['category_id'];
        }

        $sql .= ' ORDER BY tasks.status ASC,
                  CASE WHEN tasks.due_date IS NULL THEN 1 ELSE 0 END,
                  tasks.due_date ASC,
                  tasks.created_at DESC';

        $statement = Database::connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public static function findOwned(int $id, int $userId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT tasks.*, categories.name AS category_name
             FROM tasks
             LEFT JOIN categories ON categories.id = tasks.category_id
             WHERE tasks.id = :id AND tasks.user_id = :user_id'
        );
        $statement->execute(['id' => $id, 'user_id' => $userId]);
        $task = $statement->fetch();

        return $task === false ? null : $task;
    }

    public static function create(int $userId, array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO tasks (user_id, category_id, title, description, due_date, status)
             VALUES (:user_id, :category_id, :title, :description, :due_date, :status)'
        );
        $statement->execute([
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'title' => trim((string) $data['title']),
            'description' => trim((string) ($data['description'] ?? '')),
            'due_date' => $data['due_date'] ?: null,
            'status' => $data['status'] ?? 'pending',
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, int $userId, array $data): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE tasks
             SET category_id = :category_id,
                 title = :title,
                 description = :description,
                 due_date = :due_date,
                 status = :status,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'category_id' => $data['category_id'],
            'title' => trim((string) $data['title']),
            'description' => trim((string) ($data['description'] ?? '')),
            'due_date' => $data['due_date'] ?: null,
            'status' => $data['status'],
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    public static function delete(int $id, int $userId): bool
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM tasks WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute(['id' => $id, 'user_id' => $userId]);

        return $statement->rowCount() > 0;
    }

    public static function toggle(int $id, int $userId): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE tasks
             SET status = CASE WHEN status = "pending" THEN "completed" ELSE "pending" END,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute(['id' => $id, 'user_id' => $userId]);

        return $statement->rowCount() > 0;
    }

    public static function stats(int $userId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) AS completed
             FROM tasks
             WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);
        $stats = $statement->fetch();

        return [
            'total' => (int) ($stats['total'] ?? 0),
            'pending' => (int) ($stats['pending'] ?? 0),
            'completed' => (int) ($stats['completed'] ?? 0),
        ];
    }
}
