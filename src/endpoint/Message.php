<?php
declare(strict_types=1);

namespace App\Endpoint;

/**
 * Структура входящего сообщения от агента.
 */
final class Message
{
    public function __construct(
        /**
         * ID игры для маршрутизации.
         */
        public readonly string $gameId,
        
        /**
         * ID игрового объекта, которому адресовано сообщение.
         */
        public readonly string $objectId,
        
        /**
         * ID операции для резолва команды через IoC.
         */
        public readonly string $operationId,
        
        /**
         * Параметры операции в виде ассоциативного массива.
         * 
         * @var array<string, mixed>
         */
        public readonly array $args
    ) {}
    
    /**
     * Создание сообщения из JSON.
     * 
     * @param string $json
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON structure');
        }
        
        $requiredFields = ['gameId', 'objectId', 'operationId'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || !is_string($data[$field]) || $data[$field] === '') {
                throw new \InvalidArgumentException("Required field '{$field}' is missing or invalid");
            }
        }
        
        $args = $data['args'] ?? [];
        if (!is_array($args)) {
            throw new \InvalidArgumentException('Field "args" must be an array');
        }
        
        return new self(
            gameId: $data['gameId'],
            objectId: $data['objectId'], 
            operationId: $data['operationId'],
            args: $args
        );
    }
}
