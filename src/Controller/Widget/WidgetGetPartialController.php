<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(attribute: 'ROLE_USER')]
#[Route(path: '/widget/get_partial', name: 'widget_get_partial', methods: ['GET'])]
final class WidgetGetPartialController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        return $this->render(
            view: 'widget/partials/'.$request->query->get('type').'FormPartial.html.twig'
        );
    }
}
