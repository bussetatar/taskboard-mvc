<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    public static function create(string $name, string $email, string $password): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
        );
        $statement->execute([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email, created_at FROM users WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public static function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email, password, created_at FROM users WHERE email = :email'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }
}
