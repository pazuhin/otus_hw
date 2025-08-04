<?php
declare(strict_types=1);

namespace Tests;

use App\command\CheckFuelCommand;
use App\exception\command\CommandException;
use App\Game\CheckFuelableInterface;
use PHPUnit\Framework\TestCase;

final class CheckFuelTest extends TestCase
{
    public function testFuelIsEmpty(): void
    {
        $checkFuel = $this->createMock(CheckFuelableInterface::class);
        $checkFuel->method('checkFuel')->willReturn(false);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Fuel is empty');

        (new CheckFuelCommand($checkFuel))->execute();
    }

    public function testFuelIsAvailable(): void
    {
        $checkFuel = $this->createMock(CheckFuelableInterface::class);
        $checkFuel->method('checkFuel')->willReturn(true);

        $command = new CheckFuelCommand($checkFuel);
        $command->execute();
        $this->assertTrue(true);
    }
}