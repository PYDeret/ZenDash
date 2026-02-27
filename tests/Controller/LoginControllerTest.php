<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(id: 'doctrine.orm.entity_manager');
        $this->translator = $container->get(id: TranslatorInterface::class);
        $userRepository = $em->getRepository(User::class);

        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(id: 'security.user_password_hasher');

        $user = (new User())
            ->setEmail(email: 'me@example.com')
            ->setNickname(nickname: 'randomNickname')
        ;

        $user->setPassword(
            password: $passwordHasher->hashPassword(
                user: $user,
                plainPassword: 'password'
            )
        );

        $em->persist($user);
        $em->flush();
    }

    public function testLogin(): void
    {
        $this->client->request(method: 'GET', uri: '/authenticate');
        self::assertResponseIsSuccessful();

        $this->client->submitForm(
            button: $this->translator->trans(id: 'label.connect', domain: 'authentication'),
            fieldValues: [
                '_username' => 'doesNotExist@example.com',
                '_password' => 'password',
            ]
        );

        self::assertResponseRedirects('/authenticate');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.card-panel.red', $this->translator->trans(id: 'error.invalid_credentials', domain: 'authentication'));

        $this->client->request(method: 'GET', uri: '/authenticate');
        $this->client->submitForm(
            button: $this->translator->trans(id: 'label.connect', domain: 'authentication'),
            fieldValues: [
                '_username' => 'me@example.com',
                '_password' => 'password',
            ]
        );

        self::assertResponseRedirects(expectedLocation: '/home');
        $this->client->followRedirect();
        self::assertSelectorNotExists(selector: '.card-panel.red');
    }
}
