<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    public function render(string $view, array $data = []): void
    {
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        $flashSuccess = Session::pull('success');
        $flashError = Session::pull('error');
        $errors = Session::pull('errors', []);
        $old = Session::pull('old', []);
        $currentUser = Auth::user();

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        require BASE_PATH . '/app/Views/layouts/app.php';
    }

    public function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    public function invalid(string $path, array $errors, array $old = []): never
    {
        Session::put('errors', $errors);
        Session::put('old', $old);
        $this->redirect($path);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Page not found']);
    }
}
