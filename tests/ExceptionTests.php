<?php
declare(strict_types=1);

namespace Tests;

use App\command\CommandInterface;
use App\command\CommandQueue;
use App\command\MoveCommand;
use App\command\RepeatCommand;
use App\command\RepeatFailedTwiceCommand;
use App\command\WriteLogCommand;
use App\exception\ArrayLogger;
use App\exception\handler\ExceptionHandlerRegistry;
use App\exception\LoggerInterface;
use App\exception\MoveException;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;

final class ExceptionTests extends TestCase
{
    #[NoReturn] public function testRepeatThenLog(): void
    {
        ExceptionHandlerRegistry::register(
            MoveCommand::class,
            MoveException::class,
            function (CommandInterface $cmd, \Throwable $e, LoggerInterface $logger): CommandInterface {
                if ($cmd instanceof MoveCommand) {
                    if ($cmd->getTry() === 2) {
                        return $cmd;
                    } else {
                        return new WriteLogCommand($logger, new MoveException("failed twice"), new MoveCommand($logger));
                    }
                }
            }
        );

        $logger = new ArrayLogger();
        $queue = new CommandQueue($logger);
        $queue->add(new MoveCommand($logger));
        $queue->run();

        $messages = $logger->getMessages();
        $this->assertStringContainsString('[CMD] Executing App\command\MoveCommand attempt 1', implode("\n", $messages));
        $this->assertStringContainsString('[CMD] Executing App\command\MoveCommand attempt 2', implode("\n", $messages));
        $this->assertStringContainsString('[LOG] Exception: failed twice from App\command\MoveCommand', implode("\n", $messages));
    }

    public function testRepeatFailedTwiceCommand(): void
    {
        $logger = new ArrayLogger();
        $queue = new CommandQueue($logger);
        $queue->add(new RepeatFailedTwiceCommand(new MoveCommand($logger), $logger));
        $queue->run();

        $messages = $logger->getMessages();

        $this->assertStringContainsString('[FAIL] Command failed twice', implode("\n", $messages));
    }

    public function testLogExceptionCommand(): void
    {
        $logger = new ArrayLogger();
        $queue = new CommandQueue($logger);
        $queue->add(new WriteLogCommand($logger, new MoveException("some error"), new MoveCommand($logger)));
        $queue->run();

        $messages = $logger->getMessages();

        $this->assertStringContainsString('[LOG] Exception: some error', implode("\n", $messages));
    }

    #[NoReturn] public function testRepeatCommandToQueue(): void
    {
        ExceptionHandlerRegistry::register(
            MoveCommand::class,
            MoveException::class,
            function (CommandInterface $cmd, \Throwable $e, LoggerInterface $logger): CommandInterface {
                return new RepeatCommand($cmd);
            }
        );
        $logger = new ArrayLogger();
        $queue = new CommandQueue($logger);
        $queue->add(new MoveCommand($logger));
        $queue->run();

        $messages = $logger->getMessages();
        $this->assertStringContainsString('[CMD] Executing App\command\MoveCommand attempt 1', implode("\n", $messages));
        $this->assertStringContainsString('[CMD] Executing App\command\MoveCommand attempt 2', implode("\n", $messages));
        $this->assertStringContainsString('[LOG] Exception: Failed on 2 try from App\command\RepeatCommand', implode("\n", $messages));
    }

    public function testRepeatCommand(): void
    {
        $logger = new ArrayLogger();
        $queue = new CommandQueue($logger);
        $command = new MoveCommand($logger);
        $queue->add(new RepeatCommand($command));
        $queue->run();

        $messages = $logger->getMessages();
        $this->assertStringContainsString('[CMD] Executing App\command\MoveCommand attempt 1', implode("\n", $messages));
    }

    public function testLogCommandToQueue(): void
    {
        ExceptionHandlerRegistry::register(
            MoveCommand::class,
            MoveException::class,
            function (CommandInterface $cmd, \Throwable $e, LoggerInterface $logger): CommandInterface {
                /** @var MoveCommand $cmd */
                return new WriteLogCommand($logger, $e, $cmd);
            }
        );
        $logger = new ArrayLogger();
        $queue = new CommandQueue($logger);
        $queue->add(new MoveCommand($logger));
        $queue->run();

        $messages = $logger->getMessages();
        $this->assertStringContainsString('[CMD] Executing App\command\MoveCommand attempt 1', implode("\n", $messages));
        $this->assertStringContainsString('[LOG] Exception: Failed on 1 try from App\command\MoveCommand', implode("\n", $messages));
    }
}