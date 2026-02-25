<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Entity\User;
use App\Entity\Widget;
use App\Form\Widget\WidgetFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/home', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $widget = new Widget();
        $widgetForm = $this->createForm(WidgetFormType::class, $widget);
        $widgetForm->handleRequest($request);

        if ($widgetForm->isSubmitted() && $widgetForm->isValid() && $this->getUser() instanceof User) {
            $widget->setUser($this->getUser());
            $this->entityManager->persist($widget);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'widgetForm' => $widgetForm,
            'widgets' => $this->entityManager->getRepository(Widget::class)->findAll(),
        ], new Response(null, $widgetForm->isSubmitted() ? 422 : 200));
    }
}
