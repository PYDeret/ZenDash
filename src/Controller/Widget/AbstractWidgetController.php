<?php

declare(strict_types=1);

namespace App\Controller\Widget;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Turbo\TurboBundle;

abstract class AbstractWidgetController extends AbstractController
{
    /** @param array<string, mixed> $parameters */
    public function renderTurboStream(string $view, int $status, array $parameters): Response
    {
        return $this->render(
            view: $view,
            parameters: $parameters,
            response: new Response(content: '', status: $status, headers: ['Content-Type' => TurboBundle::STREAM_MEDIA_TYPE])
        );
    }
}
