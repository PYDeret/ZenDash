<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/logout', name: 'app_logout')]
class LogoutController
{
    public function __invoke(): void
    {
        throw new \LogicException(message: '');
    }
}
