<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

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

#[Route(path: '/register', name: 'app_register')]
final class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly AuthenticationUtils $authenticationUtils,
    ) {
    }

    public function __invoke(Request $request): ?Response
    {
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

        return $this->render(view: 'authentication/authenticate.html.twig', parameters: [
            'registrationForm' => $registrationForm,
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'lastUsername' => $this->authenticationUtils->getLastUsername(),
        ]);
    }
}
