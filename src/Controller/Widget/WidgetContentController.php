<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use App\Enum\Widget\WidgetTypeEnum;
use App\Form\Widget\Content\WidgetNoteContentType;
use App\Form\Widget\Content\WidgetTodoContentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(attribute: 'ROLE_USER')]
#[Route(path: '/widget/content', name: 'widget_content', methods: ['GET'])]
final class WidgetContentController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        switch (WidgetTypeEnum::tryFrom(value: $request->query->get('type', ''))) {
            case WidgetTypeEnum::NOTE:
            default:
                return $this->render(
                    view: 'widget/partials/noteFormPartial.html.twig', parameters: [
                        'contentForm' => $this->createForm(type: WidgetNoteContentType::class),
                    ]
                );

            case WidgetTypeEnum::TODO:
                return $this->render(
                    view: 'widget/partials/todoFormPartial.html.twig', parameters: [
                        'contentForm' => $this->createForm(type: WidgetTodoContentType::class),
                    ]
                );
        }
    }
}
