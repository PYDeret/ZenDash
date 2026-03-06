<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use App\Enum\Widget\WidgetTypeEnum;
use App\Form\Widget\WidgetFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(attribute: 'ROLE_USER')]
#[Route(path: '/widget/content', name: 'widget_content', methods: ['GET'])]
final class WidgetContentController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function __invoke(Request $request): Response
    {
        $type = WidgetTypeEnum::tryFrom(value: $request->query->get('type', ''));

        if ($type === null) {
            throw new BadRequestHttpException($this->translator->trans('error.wrong_call', [], 'main'));
        }

        $formView = $this->createForm(
            type: WidgetFormType::class,
            options: ['widget_type' => $type]
        )->createView();

        return $this->render(
            view: 'widget/partials/'.$type->value.'FormPartial.html.twig', parameters: [
                'contentForm' => $formView['content'],
            ]
        );
    }
}
