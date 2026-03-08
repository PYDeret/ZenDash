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
    private const NOTE_TEXT = 'Test note content';
    private const TODO_TASK = 'Test todo task';

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

    public function testAddNoteWidgetViaTurboStream(): void
    {
        $user = $this->container->get(UserRepository::class)->findOneByEmail('me@example.com');
        self::assertNotNull($user);

        $this->client->loginUser(user: $user);

        $csrfToken = $this->getCsrfToken();

        $this->client->request(
            method: 'POST',
            uri: '/widget/create',
            parameters: [
                'widget_form' => [
                    'title' => self::WIDGET_NAME,
                    'type' => 'note',
                    'content' => ['text' => self::NOTE_TEXT],
                    '_token' => $csrfToken,
                ],
            ],
            server: [
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
        self::assertEquals(['text' => self::NOTE_TEXT], $widget->getContent());
    }

    public function testAddTodoWidgetViaTurboStream(): void
    {
        $user = $this->container->get(UserRepository::class)->findOneByEmail('me@example.com');
        self::assertNotNull($user);

        $this->client->loginUser(user: $user);

        $csrfToken = $this->getCsrfToken();

        $this->client->request(
            method: 'POST',
            uri: '/widget/create',
            parameters: [
                'widget_form' => [
                    'title' => self::WIDGET_NAME,
                    'type' => 'todo',
                    'content' => ['task' => self::TODO_TASK],
                    '_token' => $csrfToken,
                ],
            ],
            server: [
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
        self::assertEquals(['task' => self::TODO_TASK, 'done' => false], $widget->getContent());
    }

    private function getCsrfToken(): string
    {
        $crawler = $this->client->request(method: 'GET', uri: '/widget/init');

        return (string) $crawler->filter(selector: 'input[name="widget_form[_token]"]')->attr('value');
    }
}
