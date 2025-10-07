<?php
declare(strict_types=1);

namespace App\Controller;

use App\GameServer\GameServer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GameController
{
    public function __construct(
        private GameServer $gameServer
    ) {}

    public function processCommand(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['token']) || !isset($data['command'])) {
                return new JsonResponse([
                    'error' => 'Missing required fields: token, command'
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->gameServer->processGameCommand(
                $data['token'],
                $data['command'],
                $data['data'] ?? []
            );

            if (!$result['success']) {
                return new JsonResponse([
                    'error' => $result['error']
                ], Response::HTTP_UNAUTHORIZED);
            }

            return new JsonResponse($result, Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
