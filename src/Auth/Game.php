<?php
declare(strict_types=1);

namespace App\Auth;

use InvalidArgumentException;

final class Game
{
    public function __construct(
        private string $id,
        private array $participants,
        private string $organizerId,
        private int $createdAt
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function getOrganizerId(): string
    {
        return $this->organizerId;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function isParticipant(string $userId): bool
    {
        return in_array($userId, $this->participants, true);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'participants' => $this->participants,
            'organizer_id' => $this->organizerId,
            'created_at' => $this->createdAt,
        ];
    }
}
