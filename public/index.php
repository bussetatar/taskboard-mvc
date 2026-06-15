<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\TaskController;
use App\Core\Database;
use App\Core\Router;

require dirname(__DIR__) . '/bootstrap.php';

Database::migrate();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self'; form-action 'self'; base-uri 'self'; frame-ancestors 'none'");

$router = new Router();

$router->get('/', [AuthController::class, 'showLogin'], ['guest']);
$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->get('/register', [AuthController::class, 'showRegister'], ['guest']);
$router->post('/register', [AuthController::class, 'register'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

$router->post('/categories', [CategoryController::class, 'store'], ['auth']);
$router->post('/categories/{id}/update', [CategoryController::class, 'update'], ['auth']);
$router->post('/categories/{id}/delete', [CategoryController::class, 'delete'], ['auth']);

$router->get('/tasks/create', [TaskController::class, 'create'], ['auth']);
$router->post('/tasks', [TaskController::class, 'store'], ['auth']);
$router->get('/tasks/{id}/edit', [TaskController::class, 'edit'], ['auth']);
$router->post('/tasks/{id}/update', [TaskController::class, 'update'], ['auth']);
$router->post('/tasks/{id}/delete', [TaskController::class, 'delete'], ['auth']);
$router->post('/tasks/{id}/toggle', [TaskController::class, 'toggle'], ['auth']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
