<?php
declare(strict_types=1);

namespace App\Endpoint;

use App\IoC\IoC;

/**
 * HTTP контроллер для приема сообщений от агентов.
 */
final readonly class GameController
{
    public function __construct(
        private IoC $ioc
    ) {}

    public function handleCommand(string $requestBody): array
    {
        try {
            $message = Message::fromJson($requestBody);
            $interpretCommand = new InterpretCommand($message, $this->ioc);
            $interpretCommand->execute();
            
            return [
                'status' => 'success',
                'message' => 'Command processed successfully',
                'data' => [
                    'gameId' => $message->gameId,
                    'objectId' => $message->objectId,
                    'operationId' => $message->operationId
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Internal server error: ' . $e->getMessage()
            ];
        }
    }
}
