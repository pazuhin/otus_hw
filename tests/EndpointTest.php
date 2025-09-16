<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\IoC\IoC;
use App\endpoint\Message;
use App\endpoint\InterpretCommand;
use App\endpoint\GameController;
use App\command\CommandQueue;
use App\command\CommandInterface;
use App\exception\ArrayLogger;
use stdClass;

/**
 * Тесты для endpoint системы.
 */
final class EndpointTest extends TestCase
{
    private IoC $ioc;
    
    protected function setUp(): void
    {
        $this->ioc = new IoC();
        $this->ioc->reset();
        $this->setupGameServices();
    }
    
    private function setupGameServices(): void
    {
        $games = [];
        
        $this->ioc->resolve('IoC.Register', 'Game.Create', function() use (&$games): string {
            $gameId = 'game_' . uniqid();
            $logger = new ArrayLogger();
            $games[$gameId] = ['objects' => [], 'queue' => new CommandQueue($logger)];
            return $gameId;
        })->execute();
        
        // Регистрируем сервис получения объекта
        $this->ioc->resolve('IoC.Register', 'Game.GetObject', function(string $gameId, string $objectId) use (&$games): stdClass {
            if (!isset($games[$gameId])) {
                throw new \RuntimeException("Game '{$gameId}' not found");
            }
            if (!isset($games[$gameId]['objects'][$objectId])) {
                throw new \RuntimeException("Object '{$objectId}' not found in game '{$gameId}'");
            }
            return $games[$gameId]['objects'][$objectId];
        })->execute();
        
        // Регистрируем сервис создания объекта
        $this->ioc->resolve('IoC.Register', 'Game.CreateObject', function(string $gameId, string $objectId) use (&$games): stdClass {
            if (!isset($games[$gameId])) {
                throw new \RuntimeException("Game '{$gameId}' not found");
            }
            $obj = new stdClass();
            $obj->id = $objectId;
            $games[$gameId]['objects'][$objectId] = $obj;
            return $obj;
        })->execute();
        
        // Регистрируем сервис добавления команды в очередь
        $this->ioc->resolve('IoC.Register', 'Game.EnqueueCommand', function(string $gameId, mixed $command) use (&$games): void {
            if (!isset($games[$gameId])) {
                throw new \RuntimeException("Game '{$gameId}' not found");
            }
            $games[$gameId]['queue']->add($command);
        })->execute();
        
        // Команда установки свойства
        $this->ioc->resolve('IoC.Register', 'Object.SetProperty', function(stdClass $obj, string $prop, mixed $value): void {
            $obj->$prop = $value;
        })->execute();

        // Тестовая команда
        $this->ioc->resolve('IoC.Register', 'TestCommand', function(stdClass $obj): CommandInterface {
            return new class implements CommandInterface {
                #[\Override]
                public function execute(): void {
                    // Простая тестовая команда
                }
            };
        })->execute();
        
        // Логгер
        $this->ioc->resolve('IoC.Register', 'Logger.Error', function(string $message): void {
            // В тестах не логируем
        })->execute();
    }
    
    public function testMessageFromJson(): void
    {
        $json = '{"gameId":"test","objectId":"obj1","operationId":"move","args":{"x":10}}';
        $message = Message::fromJson($json);
        
        $this->assertEquals('test', $message->gameId);
        $this->assertEquals('obj1', $message->objectId);
        $this->assertEquals('move', $message->operationId);
        $this->assertEquals(['x' => 10], $message->args);
    }
    
    public function testMessageFromJsonInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Message::fromJson('{"gameId":""}');
    }
    
    public function testMessageFromJsonInvalidJson(): void
    {
        $this->expectException(\JsonException::class);
        Message::fromJson('invalid json');
    }
    
    public function testInterpretCommandSuccess(): void
    {
        // Создаем игру и объект
        $gameId = $this->ioc->resolve('Game.Create')->execute();
        $this->ioc->resolve('Game.CreateObject', $gameId, 'ship_1')->execute();
        
        $message = new Message(
            gameId: $gameId,
            objectId: 'ship_1',
            operationId: 'TestCommand',
            args: ['velocity' => 5]
        );
        
        $command = new InterpretCommand($message, $this->ioc);
        $command->execute();
        
        // Проверяем, что команда выполнилась без ошибок
        $this->assertTrue(true);
    }
    
    public function testInterpretCommandNonexistentGame(): void
    {
        $message = new Message(
            gameId: 'nonexistent',
            objectId: 'ship_1',
            operationId: 'TestCommand',
            args: []
        );
        
        $command = new InterpretCommand($message, $this->ioc);
        
        $this->expectException(\RuntimeException::class);
        $command->execute();
    }
    
    public function testInterpretCommandNonexistentObject(): void
    {
        $gameId = $this->ioc->resolve('Game.Create')->execute();
        
        $message = new Message(
            gameId: $gameId,
            objectId: 'nonexistent_object',
            operationId: 'TestCommand',
            args: []
        );
        
        $command = new InterpretCommand($message, $this->ioc);
        
        $this->expectException(\RuntimeException::class);
        $command->execute();
    }

    public function testGameControllerHandleCommandSuccess(): void
    {
        $gameId = $this->ioc->resolve('Game.Create')->execute();
        $this->ioc->resolve('Game.CreateObject', $gameId, 'ship_1')->execute();

        $controller = new GameController($this->ioc);

        $requestBody = json_encode([
            'gameId' => $gameId,
            'objectId' => 'ship_1',
            'operationId' => 'TestCommand',
            'args' => ['velocity' => 10]
        ]);

        $result = $controller->handleCommand($requestBody);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals($gameId, $result['data']['gameId']);
    }

    public function testGameControllerHandleCommandInvalidJson(): void
    {
        $controller = new GameController($this->ioc);

        $result = $controller->handleCommand('invalid json');

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('Internal server error', $result['message']);
    }
}
