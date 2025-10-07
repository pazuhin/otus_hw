<?php
declare(strict_types=1);

namespace Tests;

use App\Auth\JWTService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class JWTServiceTest extends TestCase
{
    private JWTService $jwtService;
    private string $secretKey = 'test-secret-key';

    protected function setUp(): void
    {
        $this->jwtService = new JWTService($this->secretKey);
    }

    public function testGenerateAndValidateToken(): void
    {
        $userId = 'user123';
        $gameId = 'game456';
        
        $token = $this->jwtService->generateToken($userId, $gameId);
        
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        
        $payload = $this->jwtService->validateToken($token);
        
        $this->assertEquals($userId, $payload['user_id']);
        $this->assertEquals($gameId, $payload['game_id']);
        $this->assertEquals('space-battle-auth-service', $payload['iss']);
        $this->assertEquals('space-battle-game-server', $payload['aud']);
    }

    public function testExtractGameId(): void
    {
        $userId = 'user123';
        $gameId = 'game456';
        
        $token = $this->jwtService->generateToken($userId, $gameId);
        $extractedGameId = $this->jwtService->extractGameId($token);
        
        $this->assertEquals($gameId, $extractedGameId);
    }

    public function testExtractUserId(): void
    {
        $userId = 'user123';
        $gameId = 'game456';
        
        $token = $this->jwtService->generateToken($userId, $gameId);
        $extractedUserId = $this->jwtService->extractUserId($token);
        
        $this->assertEquals($userId, $extractedUserId);
    }

    public function testInvalidTokenThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JWT token');
        
        $this->jwtService->validateToken('invalid-token');
    }

    public function testExpiredTokenThrowsException(): void
    {
        $userId = 'user123';
        $gameId = 'game456';
        
        // Generate token with 1 second expiration
        $token = $this->jwtService->generateToken($userId, $gameId, 1);
        
        // Wait for token to expire
        sleep(2);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JWT token');
        
        $this->jwtService->validateToken($token);
    }

    public function testExtractGameIdFromInvalidToken(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JWT token');
        
        $this->jwtService->extractGameId('invalid-token');
    }

    public function testExtractUserIdFromInvalidToken(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JWT token');
        
        $this->jwtService->extractUserId('invalid-token');
    }
}
