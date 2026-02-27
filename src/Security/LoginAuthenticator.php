<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_auth';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get(key: '_username', default: '');
        if (!is_string($email)) {
            throw new \InvalidArgumentException(message: $this->translator->trans(id: 'error.email_type', domain: 'authentication'));
        }

        $password = $request->request->get(key: '_password', default: '');
        if (!is_string($password)) {
            throw new \InvalidArgumentException(message: $this->translator->trans(id: 'error.password_type', domain: 'authentication'));
        }

        $csrfToken = $request->request->get(key: '_csrf_token');
        if (!is_string($csrfToken)) {
            throw new \InvalidArgumentException(message: $this->translator->trans(id: 'error.csrf_type', domain: 'authentication'));
        }

        $request->getSession()->set(name: '_security.last_username', value: $email);

        return new Passport(
            userBadge: new UserBadge($email),
            credentials: new PasswordCredentials($password),
            badges: [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath(session: $request->getSession(), firewallName: $firewallName)) {
            return new RedirectResponse(url: $targetPath);
        }

        return new RedirectResponse(url: $this->urlGenerator->generate(name: 'app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(name: self::LOGIN_ROUTE);
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get(key: '_route') && $request->isMethod(method: 'POST') && $request->request->has(key: '_username');
    }
}
