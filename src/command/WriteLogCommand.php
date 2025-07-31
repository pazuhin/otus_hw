<?php
declare(strict_types=1);

namespace App\command;

use App\exception\LoggerInterface;

final readonly class WriteLogCommand implements CommandInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private \Throwable $exception,
        private CommandInterface $failedCommand,
    ) {}

    public function execute(): void
    {
        $this->logger->log("[LOG] Exception: {$this->exception->getMessage()} from " . get_class($this->failedCommand) . "\n");
    }
}