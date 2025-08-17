<?php
declare(strict_types=1);

namespace App\Сommand;

use App\Exception\LoggerInterface;
use App\Exception\MoveException;

final class MoveCommand implements CommandInterface
{
    protected int $try = 1;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(): void
    {
        $this->logger->log("[CMD] Executing " . get_class($this) ." attempt $this->try\n");
        $tryExc = $this->getTry();
        $this->incCountTry();
        throw new MoveException("Failed on $tryExc try");
    }

    public function incCountTry(): void
    {
        $this->try++;
    }

    public function getTry(): int
    {
        return $this->try;
    }
}
