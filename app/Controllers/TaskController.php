<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Task;

final class TaskController extends Controller
{
    public function create(): void
    {
        $this->render('tasks/form', [
            'title' => 'New task',
            'heading' => 'Create task',
            'action' => '/tasks',
            'task' => null,
            'categories' => Category::allByUser((int) Auth::id()),
        ]);
    }

    public function store(): void
    {
        $input = $this->input();
        $errors = $this->validate($input);

        if ($errors !== []) {
            $this->invalid('/tasks/create', $errors, $input);
        }

        Task::create((int) Auth::id(), $input);
        Session::put('success', 'Task created.');
        $this->redirect('/dashboard');
    }

    public function edit(string $id): void
    {
        $task = Task::findOwned((int) $id, (int) Auth::id());

        if ($task === null) {
            $this->notFound();
            return;
        }

        $this->render('tasks/form', [
            'title' => 'Edit task',
            'heading' => 'Edit task',
            'action' => '/tasks/' . (int) $id . '/update',
            'task' => $task,
            'categories' => Category::allByUser((int) Auth::id()),
        ]);
    }

    public function update(string $id): void
    {
        if (Task::findOwned((int) $id, (int) Auth::id()) === null) {
            $this->notFound();
            return;
        }

        $input = $this->input();
        $errors = $this->validate($input);

        if ($errors !== []) {
            $this->invalid('/tasks/' . (int) $id . '/edit', $errors, $input);
        }

        Task::update((int) $id, (int) Auth::id(), $input);
        Session::put('success', 'Task updated.');
        $this->redirect('/dashboard');
    }

    public function delete(string $id): void
    {
        if (!Task::delete((int) $id, (int) Auth::id())) {
            $this->notFound();
            return;
        }

        Session::put('success', 'Task deleted.');
        $this->redirect('/dashboard');
    }

    public function toggle(string $id): void
    {
        if (!Task::toggle((int) $id, (int) Auth::id())) {
            $this->notFound();
            return;
        }

        Session::put('success', 'Task status updated.');
        $this->redirect('/dashboard');
    }

    private function input(): array
    {
        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'category_id' => ($_POST['category_id'] ?? '') === '' ? null : (int) $_POST['category_id'],
            'due_date' => trim((string) ($_POST['due_date'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'pending'),
        ];
    }

    private function validate(array $input): array
    {
        $errors = Validator::task($input);

        if ($input['category_id'] !== null
            && Category::findOwned((int) $input['category_id'], (int) Auth::id()) === null) {
            $errors['category_id'] = 'Select one of your own categories.';
        }

        return $errors;
    }
}
