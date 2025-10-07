<?php
declare(strict_types=1);

namespace Tests;

use App\Auth\AuthService;
use App\Auth\InMemoryGameRepository;
use App\Auth\JWTService;
use App\Controller\AuthController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class AuthControllerTest extends TestCase
{
    private AuthController $controller;
    private AuthService $authService;

    protected function setUp(): void
    {
        $jwtService = new JWTService('test-secret-key');
        $gameRepository = new InMemoryGameRepository();
        $this->authService = new AuthService($jwtService, $gameRepository);
        $this->controller = new AuthController($this->authService);
    }

    public function testCreateGameSuccessfully(): void
    {
        $requestData = [
            'organizer_id' => 'organizer123',
            'participants' => ['organizer123', 'player1', 'player2']
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->createGame($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('game_id', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Game created successfully', $responseData['message']);
    }

    public function testCreateGameWithMissingFields(): void
    {
        $requestData = [
            'organizer_id' => 'organizer123'
            // Missing participants
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->createGame($request);
        
        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testGenerateTokenSuccessfully(): void
    {
        // First create a game
        $gameId = $this->authService->createGame('organizer123', ['organizer123', 'player1']);
        
        $requestData = [
            'user_id' => 'player1',
            'game_id' => $gameId
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->generateToken($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Token generated successfully', $responseData['message']);
    }

    public function testGenerateTokenForNonParticipant(): void
    {
        // First create a game
        $gameId = $this->authService->createGame('organizer123', ['organizer123', 'player1']);
        
        $requestData = [
            'user_id' => 'player2', // Not a participant
            'game_id' => $gameId
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->generateToken($request);
        
        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testValidateTokenSuccessfully(): void
    {
        // First create a game and generate token
        $gameId = $this->authService->createGame('organizer123', ['organizer123', 'player1']);
        $token = $this->authService->generateTokenForGame('player1', $gameId);
        
        $requestData = [
            'token' => $token,
            'game_id' => $gameId
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->validateToken($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['valid']);
        $this->assertEquals('Token is valid', $responseData['message']);
    }

    public function testValidateTokenWithInvalidToken(): void
    {
        $requestData = [
            'token' => 'invalid-token',
            'game_id' => 'game123'
        ];
        
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestData));
        
        $response = $this->controller->validateToken($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['valid']);
        $this->assertEquals('Token is invalid', $responseData['message']);
    }
}
