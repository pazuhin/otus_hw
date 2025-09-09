<?php

namespace Tests;

use App\command\CommandInterface;
use App\command\HardStopCommand;
use App\command\SoftStopCommand;
use App\ServerThread;
use PHPUnit\Framework\TestCase;

class ServerThreadTest extends TestCase
{
    public function testStartAndExecuteCommands(): void
    {
        $server = new ServerThread();

        $mock = $this->createMock(CommandInterface::class);
        $mock->expects($this->once())
            ->method('execute');

        $server->addCommand($mock);
        $server->addCommand(new HardStopCommand($server));

        $server->run();
    }

    public function testHardStopStopsImmediately(): void
    {
        $server = new ServerThread();

        // мок, который не должен выполниться
        $mock = $this->createMock(CommandInterface::class);
        $mock->expects($this->never())
            ->method('execute');

        $server->addCommand(new HardStopCommand($server));
        $server->addCommand($mock);

        $server->run();
    }

    public function testSoftStopWaitsUntilQueueEmpty(): void
    {
        $server = new ServerThread();

        $mock1 = $this->createMock(CommandInterface::class);
        $mock1->expects($this->once())
            ->method('execute');

        $mock2 = $this->createMock(CommandInterface::class);
        $mock2->expects($this->once())
            ->method('execute');

        $server->addCommand($mock1);
        $server->addCommand(new SoftStopCommand($server));
        $server->addCommand($mock2);

        $server->run();
    }

    public function testExceptionsAreCaughtAndServerContinues(): void
    {
        $server = new ServerThread();

        $failing = $this->createMock(CommandInterface::class);
        $failing->expects($this->once())
            ->method('execute')
            ->willThrowException(new \Exception("fail"));

        $success = $this->createMock(CommandInterface::class);
        $success->expects($this->once())
            ->method('execute');

        $server->addCommand($failing);
        $server->addCommand($success);
        $server->addCommand(new SoftStopCommand($server));

        $server->run();
    }
}