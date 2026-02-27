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
use Symfony\UX\Turbo\TurboBundle;

final class HomeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/home', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $widgetRepository = $this->entityManager->getRepository(Widget::class);
        $widget = new Widget();
        $widgetForm = $this->createForm(type: WidgetFormType::class, data: $widget);
        $widgetForm->handleRequest(request: $request);

        if ($user instanceof User && $widgetForm->isSubmitted() && $widgetForm->isValid()) {
            $widget->setUser(user: $user);
            $widget->setPosition(position: $widgetRepository->findLastestPositionByUser(user: $user));
            $this->entityManager->persist($widget);
            $this->entityManager->flush();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                return $this->render(
                    view: 'broadcast/Widget.stream.html.twig',
                    parameters: [
                        'widget' => $widget,
                    ],
                    response: new Response(content: '', status: 200, headers: ['Content-Type' => TurboBundle::STREAM_MEDIA_TYPE])
                );
            }

            return $this->redirectToRoute(route: 'app_home');
        }

        return $this->render(
            view: 'home/index.html.twig',
            parameters: [
                'widgetForm' => $widgetForm,
                'widgets' => $widgetRepository->findBy(['user' => $user]),
            ],
            response: new Response(content: null, status: $widgetForm->isSubmitted() ? 422 : 200)
        );
    }
}
