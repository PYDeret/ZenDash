<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Component\Routing\Attribute\Route;

class LogoutController
{
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
