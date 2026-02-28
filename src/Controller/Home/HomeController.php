<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Entity\Widget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[IsGranted(attribute: 'ROLE_USER')]
    #[Route(path: '/home', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $widgetRepository = $this->entityManager->getRepository(Widget::class);

        return $this->render(
            view: 'home/index.html.twig',
            parameters: [
                'widgets' => $widgetRepository->findBy(['user' => $user]),
            ],
        );
    }
}
