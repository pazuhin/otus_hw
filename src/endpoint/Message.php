<?php
declare(strict_types=1);

namespace App\endpoint;

/**
 * Структура входящего сообщения от агента.
 */
final readonly class Message
{
    public function __construct(
        public string $gameId,
        public string $objectId,
        public string $operationId,
        
        /**
         * @var array<string, mixed>
         */
        public array  $args
    ) {}
    
    /**
     * @param string $json
     * @return self
     * @throws \InvalidArgumentException
     * @psalm-suppress PossiblyUndefinedArrayOffset
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

        $gameId = (string) $data['gameId'];
        $objectId = (string) $data['objectId'];
        $operationId = (string) $data['operationId'];
        
        return new self(
            gameId: $gameId,
            objectId: $objectId, 
            operationId: $operationId,
            args: $args
        );
    }
}
