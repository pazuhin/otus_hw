<?php
declare(strict_types=1);

namespace App\command;

use App\exception\command\CommandException;
use App\Game\BurnFuelableInterface;

final readonly class BurnFuelCommand implements CommandInterface
{
    private const EPSILON = 1e-10;

    public function __construct(
        private BurnFuelableInterface $fuelable,
    ) { }

    public function execute(): void
    {
        $fuel = $this->fuelable->getFuelCount();
        $fuelVelocity = $this->fuelable->getFuelVelocity();
        $newFuelCount = $fuel * (1 - $fuelVelocity);
        if ($newFuelCount <= self::EPSILON) {
            throw new CommandException('Fuel is empty');
        }
        $this->fuelable->setFuelCount($newFuelCount);
    }
}