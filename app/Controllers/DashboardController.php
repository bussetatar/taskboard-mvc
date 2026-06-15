<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Task;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $userId = (int) Auth::id();
        $filters = [
            'status' => (string) ($_GET['status'] ?? ''),
            'category_id' => (string) ($_GET['category_id'] ?? ''),
        ];

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'categories' => Category::allByUser($userId),
            'tasks' => Task::allByUser($userId, $filters),
            'stats' => Task::stats($userId),
            'filters' => $filters,
        ]);
    }
}
