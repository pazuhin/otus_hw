<?php
declare(strict_types=1);

namespace Tests;

use App\command\BurnFuelCommand;
use App\command\ChangeVelocityCommand;
use App\command\CheckFuelCommand;
use App\command\CommandInterface;
use App\command\MacroRotateCommand;
use App\command\MoveCommand;
use App\command\MoveMacroCommand;
use App\command\RotateCommand;
use App\exception\command\CommandException;
use App\exception\LoggerInterface;
use App\Game\BurnFuelableInterface;
use App\Game\ChangeVelocityInterface;
use App\Game\CheckFuelableInterface;
use App\Game\Coords;
use App\Game\Rotate;
use App\Game\RotatebleInterface;
use PHPUnit\Framework\TestCase;

final class MoveMacroCommandTest extends TestCase
{
    public function testMacroCommandExecutesAllCommandsSuccessfully(): void
    {
        $checkFuelable = $this->createMock(CheckFuelableInterface::class);
        $burnFuelable = $this->createMock(BurnFuelableInterface::class);

        $checkFuelable->method('checkFuel')->willReturn(true);

        $burnFuelable->method('getFuelCount')->willReturn(100.0);
        $burnFuelable->method('getFuelVelocity')->willReturn(0.1);
        $burnFuelable->expects($this->never())
            ->method('setFuelCount');

        $logger = $this->createMock(LoggerInterface::class);

        $commands = [
            new CheckFuelCommand($checkFuelable),
            new MoveCommand($logger),
            new BurnFuelCommand($burnFuelable),
        ];

        $macro = new MoveMacroCommand($commands);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('MoveCommand failed: Failed on 1 try');

        $macro->execute();
    }

    public function testMacroCommandStopsOnCheckFuelFailure(): void
    {
        $checkFuelable = $this->createMock(CheckFuelableInterface::class);
        $checkFuelable->method('checkFuel')->willReturn(false);

        $logger = $this->createMock(LoggerInterface::class);
        $burnFuelable = $this->createMock(BurnFuelableInterface::class);

        $commands = [
            new CheckFuelCommand($checkFuelable),
            new MoveCommand($logger),
            new BurnFuelCommand($burnFuelable),
        ];

        $macro = new MoveMacroCommand($commands);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('CheckFuelCommand failed: Fuel is empty');

        $macro->execute();
    }

    public function testMacroCommandStopsOnBurnFuelFailure(): void
    {
        $checkFuelable = $this->createMock(CheckFuelableInterface::class);
        $checkFuelable->method('checkFuel')->willReturn(true);

        $burnFuelable = $this->createMock(BurnFuelableInterface::class);
        $burnFuelable->method('getFuelCount')->willReturn(0.0);
        $burnFuelable->method('getFuelVelocity')->willReturn(0.5);

        $commands = [
            new CheckFuelCommand($checkFuelable),
            $this->createMock(CommandInterface::class),
            new BurnFuelCommand($burnFuelable),
        ];

        $macro = new MoveMacroCommand($commands);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('BurnFuelCommand failed: Fuel is empty');

        $macro->execute();
    }

    public function testRotateMacroCommandWithoutChangingVelocity(): void
    {
        $checkFuelable = $this->createMock(CheckFuelableInterface::class);
        $burnFuelable = $this->createMock(BurnFuelableInterface::class);

        $checkFuelable->method('checkFuel')->willReturn(true);

        $burnFuelable->method('getFuelCount')->willReturn(100.0);
        $burnFuelable->method('getFuelVelocity')->willReturn(0.1);

        $rObject = $this->createMock(RotatebleInterface::class);

        $rObject->method('getDirection')->willReturn(2);
        $rObject->method('getAngularVelocity')->willReturn(1);
        $rObject->method('getDirectionsNumber')->willReturn(4);

        $commands = [
            new CheckFuelCommand($checkFuelable),
            new RotateCommand($rObject),
            new ChangeVelocityCommand($rObject, null),
            new BurnFuelCommand($burnFuelable),
        ];

        $macro = new MoveMacroCommand($commands);

        $macro->execute();
        $this->assertTrue(true);
    }

    public function testRotateMacroCommandWithChangingVelocity(): void
    {
        $checkFuelable = $this->createMock(CheckFuelableInterface::class);
        $burnFuelable = $this->createMock(BurnFuelableInterface::class);

        $checkFuelable->method('checkFuel')->willReturn(true);

        $burnFuelable->method('getFuelCount')->willReturn(100.0);
        $burnFuelable->method('getFuelVelocity')->willReturn(0.1);

        $rotatable = $this->createMock(RotatebleInterface::class);
        $movable = $this->createMock(ChangeVelocityInterface::class);

        $rotatable->method('getDirection')->willReturn(1);
        $rotatable->method('getAngularVelocity')->willReturn(1);
        $rotatable->method('getDirectionsNumber')->willReturn(4);

        $rotatable->expects($this->once())
            ->method('setDirection')
            ->with(2);

        $movable->expects($this->once())
            ->method('setVelocity')
            ->with($this->callback(fn($coords) => $coords instanceof Coords));

        $command = new MacroRotateCommand($rotatable, $movable);

        $commands = [
            new CheckFuelCommand($checkFuelable),
            $command,
            new BurnFuelCommand($burnFuelable),
        ];
        $macro = new MoveMacroCommand($commands);

        $macro->execute();
    }
}