<?php

declare(strict_types=1);

namespace App\Service\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Turbo\TurboBundle;

final class RequestFormatStreamService
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function checkStreamFormat(Request $request): void
    {
        if (TurboBundle::STREAM_FORMAT !== $request->getPreferredFormat()) {
            throw new \RuntimeException($this->translator->trans('error.wrong_call', [], 'main'));
        }
    }
}
