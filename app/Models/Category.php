<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    public static function allByUser(int $userId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT categories.id, categories.name, COUNT(tasks.id) AS task_count
             FROM categories
             LEFT JOIN tasks ON tasks.category_id = categories.id
             WHERE categories.user_id = :user_id
             GROUP BY categories.id, categories.name
             ORDER BY categories.name'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public static function findOwned(int $id, int $userId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, user_id, name, created_at
             FROM categories
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute(['id' => $id, 'user_id' => $userId]);
        $category = $statement->fetch();

        return $category === false ? null : $category;
    }

    public static function create(int $userId, string $name): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO categories (user_id, name) VALUES (:user_id, :name)'
        );
        $statement->execute(['user_id' => $userId, 'name' => trim($name)]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, int $userId, string $name): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE categories SET name = :name WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute(['name' => trim($name), 'id' => $id, 'user_id' => $userId]);

        return $statement->rowCount() > 0;
    }

    public static function delete(int $id, int $userId): bool
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM categories WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute(['id' => $id, 'user_id' => $userId]);

        return $statement->rowCount() > 0;
    }
}
