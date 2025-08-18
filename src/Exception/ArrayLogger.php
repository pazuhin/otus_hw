<?php
declare(strict_types=1);

namespace App\Exception;

final class ArrayLogger implements LoggerInterface
{
    private array $messages = [];

    public function log(string $errorMessage): void
    {
        $this->messages[] = $errorMessage;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
