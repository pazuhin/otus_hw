<?php
declare(strict_types=1);

namespace App\command;

use App\exception\command\CommandException;
use App\Game\CheckFuelableInterface;

final readonly class CheckFuelCommand implements CommandInterface
{
    public function __construct(
        private CheckFuelableInterface $checkFuelable,
    ) { }

    public function execute(): void
    {
        if ($this->checkFuelable->checkFuel() !== true) {
            throw new CommandException('Fuel is empty');
        }
    }
}