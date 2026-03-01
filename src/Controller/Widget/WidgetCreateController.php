<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use App\Entity\User;
use App\Entity\Widget;
use App\Form\Widget\WidgetFormType;
use App\Helper\Request\RequestFormatStreamHelper;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Turbo\TurboBundle;

final class WidgetCreateController extends AbstractController
{
    public function __construct(
        private readonly WidgetRepository $widgetRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[IsGranted(attribute: 'ROLE_USER')]
    #[Route(path: '/widget/create', name: 'widget_create', methods: ['POST'])]
    public function index(Request $request): Response
    {
        RequestFormatStreamHelper::checkStreamFormat($request, $this->translator);
        $user = $this->getUser();
        $widget = new Widget();
        $widgetForm = $this->createForm(type: WidgetFormType::class, data: $widget);
        $widgetForm->handleRequest(request: $request);

        if ($user instanceof User && $widgetForm->isSubmitted() && $widgetForm->isValid()) {
            $widget
                ->setUser(user: $user)
                ->setPosition(position: $this->widgetRepository->findLastestPositionByUser(user: $user))
            ;

            $this->entityManager->persist(object: $widget);
            $this->entityManager->flush();

            return $this->render(
                view: 'broadcast/Widget/Widget.stream.html.twig',
                parameters: [
                    'widget' => $widget,
                ],
                response: new Response(content: '', status: 200, headers: ['Content-Type' => TurboBundle::STREAM_MEDIA_TYPE]));
        }

        return $this->render(
            view: 'broadcast/widget/WidgetForm.stream.html.twig',
            parameters: [
                'widgetForm' => $widgetForm->createView(),
            ],
            response: new Response(content: '', status: 422, headers: ['Content-Type' => TurboBundle::STREAM_MEDIA_TYPE])
        );
    }
}
