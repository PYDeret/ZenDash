<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use App\Entity\Widget;
use App\Form\Widget\WidgetFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WidgetInitController extends AbstractController
{
    #[IsGranted(attribute: 'ROLE_USER')]
    #[Route(path: '/widget/init', name: 'widget_init', methods: ['GET'])]
    public function index(): Response
    {
        $widget = new Widget();
        $widgetForm = $this->createForm(
            type: WidgetFormType::class,
            data: $widget,
            options: [
                'action' => $this->generateUrl('widget_create'),
            ]
        );

        return $this->render(
            view: 'widget/widgetForm.html.twig',
            parameters: [
                'widgetForm' => $widgetForm->createView(),
            ],
        );
    }
}
