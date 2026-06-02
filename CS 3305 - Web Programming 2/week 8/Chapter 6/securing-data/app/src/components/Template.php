<?php
declare(strict_types=1);

namespace Components;

class Template
{
    public static string $viewsPath = __DIR__ . '/../templates';

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    private function getFilepath(): string
    {
        return self::$viewsPath . DIRECTORY_SEPARATOR . $this->name . '.php';
    }

    public function render(array $data = []): string
    {
        extract($data, EXTR_OVERWRITE);
        ob_start();
        require $this->getFilepath();
        return (string) ob_get_clean();
    }
}
