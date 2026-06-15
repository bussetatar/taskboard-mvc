<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use PDOException;

final class CategoryController extends Controller
{
    public function store(): void
    {
        $input = ['name' => trim((string) ($_POST['name'] ?? ''))];
        $errors = Validator::category($input);

        if ($errors !== []) {
            $this->invalid('/dashboard', $errors, $input);
        }

        try {
            Category::create((int) Auth::id(), $input['name']);
        } catch (PDOException) {
            $this->invalid('/dashboard', ['name' => 'You already have a category with this name.'], $input);
        }

        Session::put('success', 'Category created.');
        $this->redirect('/dashboard');
    }

    public function update(string $id): void
    {
        $category = Category::findOwned((int) $id, (int) Auth::id());

        if ($category === null) {
            $this->notFound();
            return;
        }

        $input = ['name' => trim((string) ($_POST['name'] ?? ''))];
        $errors = Validator::category($input);

        if ($errors !== []) {
            $this->invalid('/dashboard', $errors, $input);
        }

        try {
            Category::update((int) $id, (int) Auth::id(), $input['name']);
        } catch (PDOException) {
            $this->invalid('/dashboard', ['name' => 'You already have a category with this name.'], $input);
        }

        Session::put('success', 'Category updated.');
        $this->redirect('/dashboard');
    }

    public function delete(string $id): void
    {
        if (!Category::delete((int) $id, (int) Auth::id())) {
            $this->notFound();
            return;
        }

        Session::put('success', 'Category deleted. Its tasks were kept without a category.');
        $this->redirect('/dashboard');
    }
}
