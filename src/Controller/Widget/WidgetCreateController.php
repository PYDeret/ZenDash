<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use App\Entity\User;
use App\Entity\Widget;
use App\Enum\Widget\WidgetTypeEnum;
use App\Form\Widget\WidgetFormType;
use App\Repository\WidgetRepository;
use App\Service\Request\RequestFormatStreamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(attribute: 'ROLE_USER')]
#[Route(path: '/widget/create', name: 'widget_create', methods: ['POST'])]
final class WidgetCreateController extends AbstractWidgetController
{
    public function __construct(
        private readonly WidgetRepository $widgetRepository,
        private readonly RequestFormatStreamService $requestFormatStreamService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $this->requestFormatStreamService->checkStreamFormat($request);
        $user = $this->getUser();
        $widget = new Widget();
        $widgetForm = $this->createForm(type: WidgetFormType::class, data: $widget);
        $widgetForm->handleRequest(request: $request);

        if ($user instanceof User && $widgetForm->isSubmitted() && $widgetForm->isValid()) {
            $widget
                ->setUser(user: $user)
                ->setPosition(position: $this->widgetRepository->findLatestPositionByUser(user: $user))
            ;

            $content = $widget->getContent();
            if (is_array($content) && !isset($content['done']) && $widget->getType() === WidgetTypeEnum::TODO) {
                $widget->setContent([...$content, 'done' => false]);
            }

            $this->entityManager->persist(object: $widget);
            $this->entityManager->flush();

            return $this->renderTurboStream(
                view: 'broadcast/Widget/Widget.stream.html.twig',
                status: Response::HTTP_OK,
                parameters: [
                    'widget' => $widget,
                ],
            );
        }

        return $this->renderTurboStream(
            view: 'broadcast/Widget/WidgetForm.stream.html.twig',
            status: Response::HTTP_UNPROCESSABLE_ENTITY,
            parameters: [
                'widgetForm' => $widgetForm->createView(),
            ],
        );
    }
}
