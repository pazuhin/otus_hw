<?php
declare(strict_types=1);

namespace App\Controller;
use App\Resolver\SpaceshipOperationsResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{

    #[Route('/main')]
    public function index(SpaceshipOperationsResolver $resolver): Response
    {
        return $this->json([
            'handlers' => $resolver->getHandlers(),
        ]);
    }
}
