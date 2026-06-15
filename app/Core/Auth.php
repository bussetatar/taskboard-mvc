<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    public static function check(): bool
    {
        return self::id() !== null;
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');

        return is_numeric($id) ? (int) $id : null;
    }

    public static function user(): ?array
    {
        $id = self::id();

        return $id === null ? null : User::find($id);
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Session::put('user_id', $userId);
    }

    public static function logout(): void
    {
        Session::forget('user_id');
        Session::regenerate();
    }
}
