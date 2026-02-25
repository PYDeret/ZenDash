<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Entity\Widget;
use App\Form\Widget\WidgetFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $widget = new Widget();
        $widgetForm = $this->createForm(WidgetFormType::class, $widget);

        return $this->render('home/index.html.twig', [
            'widgetForm' => $widgetForm,
        ]);
    }
}
