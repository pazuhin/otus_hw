<?php
declare(strict_types=1);

namespace App\Controller;

use App\Auth\AuthService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function createGame(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['organizer_id']) || !isset($data['participants'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: organizer_id, participants'
                ], Response::HTTP_BAD_REQUEST);
            }

            $gameId = $this->authService->createGame(
                $data['organizer_id'],
                $data['participants']
            );

            return new JsonResponse([
                'game_id' => $gameId,
                'message' => 'Game created successfully'
            ], Response::HTTP_CREATED);

        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function generateToken(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['user_id']) || !isset($data['game_id'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: user_id, game_id'
                ], Response::HTTP_BAD_REQUEST);
            }

            $token = $this->authService->generateTokenForGame(
                $data['user_id'],
                $data['game_id']
            );

            return new JsonResponse([
                'token' => $token,
                'message' => 'Token generated successfully'
            ], Response::HTTP_OK);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function validateToken(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['token']) || !isset($data['game_id'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: token, game_id'
                ], Response::HTTP_BAD_REQUEST);
            }

            $isValid = $this->authService->validateGameAccess(
                $data['token'],
                $data['game_id']
            );

            return new JsonResponse([
                'valid' => $isValid,
                'message' => $isValid ? 'Token is valid' : 'Token is invalid'
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
