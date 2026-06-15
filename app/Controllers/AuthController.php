<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;
use PDOException;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->render('auth/login', ['title' => 'Sign in']);
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = User::findByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            $this->invalid('/login', ['email' => 'The email or password is incorrect.'], ['email' => $email]);
        }

        Auth::login((int) $user['id']);
        Session::put('success', 'Welcome back, ' . $user['name'] . '.');
        $this->redirect('/dashboard');
    }

    public function showRegister(): void
    {
        $this->render('auth/register', ['title' => 'Create account']);
    }

    public function register(): void
    {
        $input = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
        ];
        $errors = Validator::registration($input);

        if ($errors !== []) {
            unset($input['password'], $input['password_confirmation']);
            $this->invalid('/register', $errors, $input);
        }

        try {
            $userId = User::create($input['name'], $input['email'], $input['password']);
        } catch (PDOException) {
            unset($input['password'], $input['password_confirmation']);
            $this->invalid('/register', ['email' => 'An account with this email already exists.'], $input);
        }

        Auth::login($userId);
        Session::put('success', 'Your account has been created.');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::put('success', 'You have been signed out.');
        $this->redirect('/login');
    }
}
