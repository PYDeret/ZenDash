<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Widget;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\Turbo\TurboBundle;

final class HomeControllerTest extends WebTestCase
{
    private const WIDGET_NAME = 'Test Widget';

    private KernelBrowser $client;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = self::getContainer();
        $em = $this->container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $this->container->get('security.user_password_hasher');

        $user = (new User())->setEmail('me@example.com')->setNickname('randomNickname');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $em->persist($user);
        $em->flush();
    }

    public function testIndex(): void
    {
        $user = $this->container->get(UserRepository::class)->findOneByEmail('me@example.com');
        self::assertNotNull($user);

        $this->client->loginUser($user);
        $this->client->request('GET', '/home');
        self::assertResponseIsSuccessful();
    }

    public function testAddWidgetViaTurboStream(): void
    {
        $user = $this->container->get(UserRepository::class)->findOneByEmail('me@example.com');
        self::assertNotNull($user);

        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/home');
        $form = $crawler->filter('#create-widget-form-element')->form([
            'widget_form[title]' => self::WIDGET_NAME,
            'widget_form[type]' => 'note',
        ]);

        $this->client->submit($form, [], [
            'HTTP_ACCEPT' => TurboBundle::STREAM_MEDIA_TYPE,
            'QUERY_STRING' => '_format='.TurboBundle::STREAM_FORMAT,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame(
            'Content-Type',
            TurboBundle::STREAM_MEDIA_TYPE.'; charset=UTF-8'
        );
        self::assertSelectorExists('turbo-stream');

        $em = $this->container->get('doctrine.orm.entity_manager');
        $widget = $em->getRepository(Widget::class)->findOneBy(['title' => self::WIDGET_NAME]);

        self::assertNotNull($widget);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals($user->getId(), $widget->getUser()?->getId());
    }
}
