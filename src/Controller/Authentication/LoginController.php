<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Entity\User;
use App\Form\Auth\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/login', name: 'app_login')]
final class LoginController extends AbstractController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute(route: 'app_home');
        }

        $user = new User();
        $registrationForm = $this->createForm(type: RegistrationFormType::class, data: $user);
        $registrationForm->handleRequest(request: $request);
        $activeTab = 'login';
        if ($registrationForm->isSubmitted() && !$registrationForm->isValid()) {
            $activeTab = 'register';
        }

        return $this->render(view: 'authentication/authenticate.html.twig', parameters: [
            'registrationForm' => $registrationForm,
            'lastUsername' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'activeTab' => $activeTab,
        ]);
    }
}
