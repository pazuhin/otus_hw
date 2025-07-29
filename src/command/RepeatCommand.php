<?php
declare(strict_types=1);

namespace App\command;

readonly class RepeatCommand implements CommandInterface
{
    public function __construct(
        private CommandInterface $originalCommand,
    ) {}

    public function execute(): void
    {
        $this->originalCommand->execute();
    }
}