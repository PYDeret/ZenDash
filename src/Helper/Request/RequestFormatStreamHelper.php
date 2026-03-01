<?php

namespace App\Helper\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Turbo\TurboBundle;

class RequestFormatStreamHelper
{
    public static function checkStreamFormat(Request $request, TranslatorInterface $translator): void
    {
        if (TurboBundle::STREAM_FORMAT !== $request->getPreferredFormat()) {
            throw new \RuntimeException($translator->trans('error.wrong_call', [], 'main'));
        }
    }
}
