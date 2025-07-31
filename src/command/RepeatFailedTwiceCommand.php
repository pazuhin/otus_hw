<?php
declare(strict_types=1);

namespace App\command;

use App\exception\LoggerInterface;

readonly class RepeatFailedTwiceCommand implements CommandInterface
{
    public function __construct(
        private CommandInterface $failedCommand,
        private LoggerInterface $logger,
    ) {}

    public function execute(): void
    {
        $this->logger->log("[FAIL] Command failed twice: " . get_class($this->failedCommand) . "\n");
    }
}