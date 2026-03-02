<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/logout', name: 'app_logout')]
final class LogoutController
{
    public function __invoke(): void
    {
        throw new \LogicException(message: '');
    }
}
