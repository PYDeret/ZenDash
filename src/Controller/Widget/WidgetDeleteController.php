<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use App\Entity\User;
use App\Entity\Widget;
use App\Repository\WidgetRepository;
use App\Service\Request\RequestFormatStreamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Turbo\TurboBundle;

#[IsGranted(attribute: 'ROLE_USER')]
#[Route(path: '/widget/delete', name: 'widget_delete', methods: ['DELETE'])]
final class WidgetDeleteController extends AbstractController
{
    public function __construct(
        private readonly WidgetRepository $widgetRepository,
        private readonly RequestFormatStreamService $requestFormatStreamService,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();
        $this->requestFormatStreamService->checkStreamFormat($request);
        $widget = $this->widgetRepository->find($request->request->get('id'));
        if (!$widget instanceof Widget || !$user instanceof User) {
            throw new NotFoundHttpException($this->translator->trans('error.not_found', [], 'widget'));
        }

        if (!$widget->getUser() instanceof User || $widget->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException($this->translator->trans('error.wrong_action', [], 'main'));
        }

        $id = $widget->getId();
        $this->entityManager->remove($widget);
        $this->entityManager->flush();

        return $this->render(
            view: 'broadcast/Widget/Widget.stream.html.twig',
            parameters: [
                'id' => $id,
            ],
            response: new Response(
                status: 200,
                headers: ['Content-Type' => TurboBundle::STREAM_MEDIA_TYPE]
            )
        );
    }
}
