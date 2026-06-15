<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;

final class Validator
{
    public static function registration(array $input): array
    {
        $errors = [];
        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if (self::length($name) < 2 || self::length($name) > 80) {
            $errors['name'] = 'Name must contain between 2 and 80 characters.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || self::length($email) > 190) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must contain at least 8 characters.';
        }

        if ($password !== (string) ($input['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = 'Password confirmation does not match.';
        }

        return $errors;
    }

    public static function category(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));

        if ($name === '' || self::length($name) > 80) {
            return ['name' => 'Category name is required and may contain up to 80 characters.'];
        }

        return [];
    }

    public static function task(array $input): array
    {
        $errors = [];
        $title = trim((string) ($input['title'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $dueDate = trim((string) ($input['due_date'] ?? ''));
        $status = (string) ($input['status'] ?? 'pending');

        if ($title === '' || self::length($title) > 120) {
            $errors['title'] = 'Title is required and may contain up to 120 characters.';
        }

        if (self::length($description) > 1000) {
            $errors['description'] = 'Description may contain up to 1000 characters.';
        }

        if ($dueDate !== '' && !self::validDate($dueDate)) {
            $errors['due_date'] = 'Due date must use the YYYY-MM-DD format.';
        }

        if (!in_array($status, ['pending', 'completed'], true)) {
            $errors['status'] = 'Select a valid task status.';
        }

        return $errors;
    }

    private static function validDate(string $value): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
