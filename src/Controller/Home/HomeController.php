<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Entity\Widget;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    public function __construct(private readonly WidgetRepository $widgetRepository)
    {
    }

    #[IsGranted(attribute: 'ROLE_USER')]
    #[Route(path: '/home', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            view: 'home/index.html.twig',
            parameters: [
                'widgets' => $this->widgetRepository->findBy(['user' => $this->getUser()]),
            ],
        );
    }
}
