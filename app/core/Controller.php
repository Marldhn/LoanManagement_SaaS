<?php

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = APP_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die('View not found: ' . $view);
        }

        require $viewFile;
    }

    protected function redirect(string $page): void
    {
        header('Location: index.php?page=' . $page);
        exit;
    }
}