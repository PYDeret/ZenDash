<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\Auth\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly AuthenticationUtils $authenticationUtils,
    ) {
    }

    #[Route(path: '/authenticate', name: 'app_auth')]
    public function authenticate(Request $request): ?Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute(route: 'app_home');
        }

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $user = new User();
        $registrationForm = $this->createForm(type: RegistrationFormType::class, data: $user);
        $registrationForm->handleRequest(request: $request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $plainPassword = $registrationForm->get(name: 'plainPassword')->getData();
            $user->setPassword(password: $this->userPasswordHasher->hashPassword(user: $user, plainPassword: $plainPassword));
            $this->entityManager->persist(object: $user);
            $this->entityManager->flush();

            return $this->security->login(
                user: $user,
                authenticatorName: LoginAuthenticator::class,
                firewallName: 'main'
            );
        }

        $activeTab = 'login';
        if ($registrationForm->isSubmitted() && !$registrationForm->isValid()) {
            $activeTab = 'register';
        }

        return $this->render(view: 'auth/authenticate.html.twig', parameters: [
            'registrationForm' => $registrationForm,
            'lastUsername' => $lastUsername,
            'error' => $error,
            'activeTab' => $activeTab,
        ]);
    }
}
