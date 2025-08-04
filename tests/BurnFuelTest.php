<?php
declare(strict_types=1);

namespace Tests;

use App\command\BurnFuelCommand;
use App\exception\command\CommandException;
use App\Game\BurnFuelableInterface;
use PHPUnit\Framework\TestCase;

final class BurnFuelTest extends TestCase
{
    public function testBurnFuel(): void
    {
        $burlFuel = $this->createMock(BurnFuelableInterface::class);
        $burlFuel->method('getFuelCount')->willReturn(100.0);
        $burlFuel->method('getFuelVelocity')->willReturn(0.5);
        $newFuelCount = 50;
        $burlFuel->expects($this->once())
            ->method('setFuelCount')
            ->with($newFuelCount);
        $move = new BurnFuelCommand($burlFuel);
        $move->execute();
    }

    public function testBurnFuelFail(): void
    {
        $burlFuel = $this->createMock(BurnFuelableInterface::class);
        $burlFuel->method('getFuelCount')->willReturn(0.0000001);
        $burlFuel->method('getFuelVelocity')->willReturn(2.0);
        $move = new BurnFuelCommand($burlFuel);
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Fuel is empty');
        $move->execute();
    }
}