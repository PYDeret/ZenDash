<?php

declare(strict_types=1);

namespace App\Tests\Controller\Widget;

use App\Entity\User;
use App\Entity\Widget;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\Turbo\TurboBundle;

final class WidgetCreateControllerTest extends WebTestCase
{
    private const WIDGET_NAME = 'Test Widget';

    private KernelBrowser $client;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = self::getContainer();
        $em = $this->container->get(id: 'doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $this->container->get(id: 'security.user_password_hasher');

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

    public function testAddWidgetViaTurboStream(): void
    {
        $user = $this->container->get(UserRepository::class)->findOneByEmail('me@example.com');
        self::assertNotNull($user);

        $this->client->loginUser(user: $user);
        $crawler = $this->client->request(method: 'GET', uri: '/home');
        $form = $crawler->filter(selector: '#create-widget-form-element')->form(values: [
            'widget_form[title]' => self::WIDGET_NAME,
            'widget_form[type]' => 'note',
        ]);

        $this->client->submit(
            form: $form,
            serverParameters: [
                'HTTP_ACCEPT' => TurboBundle::STREAM_MEDIA_TYPE,
                'QUERY_STRING' => '_format='.TurboBundle::STREAM_FORMAT,
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', TurboBundle::STREAM_MEDIA_TYPE.'; charset=UTF-8');

        self::assertSelectorExists(selector: 'turbo-stream');
        $em = $this->container->get(id: 'doctrine.orm.entity_manager');
        $widget = $em->getRepository(Widget::class)->findOneBy(['title' => self::WIDGET_NAME]);

        self::assertNotNull($widget);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals($user->getId(), $widget->getUser()?->getId());
    }
}
