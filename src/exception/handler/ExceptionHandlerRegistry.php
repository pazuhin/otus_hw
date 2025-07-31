<?php
declare(strict_types=1);

namespace App\exception\handler;

use App\command\CommandInterface;
use App\exception\LoggerInterface;

final class ExceptionHandlerRegistry
{
    /** @var array<string, array<string, callable>> */
    private static array $store = [];

    public static function register(string $commandClass, string $exceptionClass, callable $handler): void
    {
        self::$store[$commandClass][$exceptionClass] = $handler;
    }

    public static function handle(CommandInterface $command, \Throwable $exception, LoggerInterface $logger): ?CommandInterface
    {
        $cmdClass = get_class($command);
        $exClass = get_class($exception);

        if (isset(self::$store[$cmdClass][$exClass])) {
            return (self::$store[$cmdClass][$exClass])($command, $exception, $logger);
        }

        return null;
    }
}