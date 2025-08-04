<?php
declare(strict_types=1);

namespace App\command;

use App\exception\command\CommandException;

final readonly class MoveMacroCommand implements CommandInterface
{
    /**
     * @param array<CommandInterface>  $commands
     */
    public function __construct(private array $commands)
    {
    }

    public function execute(): void
    {
        foreach ($this->commands as $command) {
            try {
                $command->execute();
            } catch (\Exception $e) {
                throw new CommandException($command::class . ' failed: ' . $e->getMessage());
            }
        }
    }
}