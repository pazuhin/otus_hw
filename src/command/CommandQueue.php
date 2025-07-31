<?php
declare(strict_types=1);

namespace App\command;

use App\exception\handler\ExceptionHandlerRegistry;
use App\exception\LoggerInterface;

final class CommandQueue
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) { }

    /** @var CommandInterface[] */
    private array $queue = [];

    public function add(CommandInterface $command): void
    {
        $this->queue[] = $command;
    }

    public function run(): void
    {
        while ($command = array_shift($this->queue)) {
            try {
                $command->execute();
            } catch (\Exception $e) {
                $newCmd = ExceptionHandlerRegistry::handle($command, $e, $this->logger);
                if ($newCmd !== null) {
                    $this->add($newCmd);
                } else {
                    $this->add(new WriteLogCommand($this->logger, $e, $command));
                }
            }
        }
    }
}