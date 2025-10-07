<?php
declare(strict_types=1);

namespace Tests;

use App\Auth\JWTService;
use App\Controller\GameController;
use App\GameServer\GameServer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class GameControllerTest extends TestCase
{
    private GameController $controller;
    private JWTService $jwtService;

    protected function setUp(): void
    {
        $this->jwtService = new JWTService('test-secret-key');
        $gameServer = new GameServer($this->jwtService);
        $this->controller = new GameController($gameServer);
    }

    public function testProcessCommandSuccessfully(): void
    {
        $userId = 'user123';
        $gameId = 'game456';
        
        $token = $this->jwtService->generateToken($userId, $gameId);
        
        $requestData = [
            'token' => $token,
            'command' => 'move',
            'data' => ['velocity' => ['x' => 5, 'y' => 3]]
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->processCommand($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals($gameId, $responseData['game_id']);
        $this->assertEquals($userId, $responseData['user_id']);
        $this->assertEquals('move', $responseData['command']);
    }

    public function testProcessCommandWithInvalidToken(): void
    {
        $requestData = [
            'token' => 'invalid-token',
            'command' => 'move',
            'data' => ['velocity' => ['x' => 5, 'y' => 3]]
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->processCommand($request);
        
        $this->assertEquals(401, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testProcessCommandWithMissingFields(): void
    {
        $requestData = [
            'command' => 'move'
            // Missing token
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->processCommand($request);
        
        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

}
