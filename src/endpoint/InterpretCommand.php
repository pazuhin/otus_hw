<?php
declare(strict_types=1);

namespace App\Endpoint;

use App\command\CommandInterface;
use App\IoC\IoC;

final readonly class InterpretCommand implements CommandInterface
{
    public function __construct(
        private Message $message,
        private IoC $ioc
    ) {}
    
    #[\Override]
    public function execute(): void
    {
        try {
            $gameObject = $this->ioc->resolve(
                'Game.GetObject', 
                $this->message->gameId, 
                $this->message->objectId
            )->execute();

            $this->initializeObjectWithArgs($gameObject, $this->message->args);

            $command = $this->ioc->resolve(
                $this->message->operationId,
                $gameObject,
                ...$this->extractCommandArgs($this->message->args)
            )->execute();

            $this->ioc->resolve(
                'Game.EnqueueCommand',
                $this->message->gameId,
                $command
            )->execute();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Инициализирует объект параметрами из args.
     * 
     * @param mixed $gameObject
     * @param array<string, mixed> $args
     */
    private function initializeObjectWithArgs(mixed $gameObject, array $args): void
    {
        foreach ($args as $key => $value) {
            if (str_starts_with($key, 'set_')) {
                $propertyName = substr($key, 4);
                $this->ioc->resolve(
                    "Object.SetProperty",
                    $gameObject,
                    $propertyName,
                    $value
                )->execute();
            }
        }
    }
    
    /**
     * Извлекает аргументы для команды из параметров сообщения.
     * 
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    private function extractCommandArgs(array $args): array
    {
        $commandArgs = [];
        
        foreach ($args as $key => $value) {
            if (!str_starts_with($key, 'set_')) {
                $commandArgs[] = $value;
            }
        }
        
        return $commandArgs;
    }
}
