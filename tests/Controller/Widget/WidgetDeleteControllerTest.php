<?php

declare(strict_types=1);

namespace App\Tests\Controller\Widget;

use App\Entity\User;
use App\Entity\Widget;
use App\Enum\Widget\WidgetTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\UX\Turbo\TurboBundle;

final class WidgetDeleteControllerTest extends WebTestCase
{
    private const WIDGET_TITLE = 'Test Widget to Delete';

    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private User $user;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $container = self::getContainer();
        $this->em = $container->get(id: 'doctrine.orm.entity_manager');

        foreach ($this->em->getRepository(Widget::class)->findAll() as $w) {
            $this->em->remove($w);
        }

        foreach ($this->em->getRepository(User::class)->findAll() as $u) {
            $this->em->remove($u);
        }

        $this->em->flush();

        $passwordHasher = $container->get(id: 'security.user_password_hasher');
        $user = (new User())
            ->setEmail(email: 'me@example.com')
            ->setNickname(nickname: 'randomNickname');

        $user->setPassword(password: $passwordHasher->hashPassword(
            user: $user,
            plainPassword: 'password'
        ));

        $this->em->persist($user);
        $this->em->flush();
        $this->user = $user;
    }

    public function testDeleteWidget(): void
    {
        $widget = (new Widget())
            ->setTitle(title: self::WIDGET_TITLE)
            ->setUser(user: $this->user)
            ->setType(WidgetTypeEnum::NOTE)
            ->setPosition(1);

        $this->em->persist($widget);
        $this->em->flush();

        $widgetId = $widget->getId();
        self::assertNotNull($widgetId);
        $this->client->loginUser(user: $this->user);

        $this->client->request(
            method: 'DELETE',
            uri: '/widget/delete',
            parameters: ['id' => $widgetId],
            server: [
                'HTTP_ACCEPT' => TurboBundle::STREAM_MEDIA_TYPE,
                'QUERY_STRING' => '_format='.TurboBundle::STREAM_FORMAT,
            ],
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', TurboBundle::STREAM_MEDIA_TYPE.'; charset=UTF-8');

        $deleted = $this->em->getRepository(Widget::class)->find($widgetId);
        self::assertNull($deleted, 'Le widget doit être supprimé de la base de données.');
    }

    public function testDeleteNonExistentWidgetThrows(): void
    {
        $this->client->catchExceptions(false);
        $this->client->loginUser($this->user);

        $this->expectException(NotFoundResourceException::class);

        $this->client->request(
            method: 'DELETE',
            uri: '/widget/delete',
            parameters: ['id' => 999999],
            server: [
                'HTTP_ACCEPT' => TurboBundle::STREAM_MEDIA_TYPE,
                'QUERY_STRING' => '_format='.TurboBundle::STREAM_FORMAT,
            ],
        );
    }

    public function testDeleteWithWrongFormatThrows(): void
    {
        $widget = (new Widget())
            ->setTitle(title: self::WIDGET_TITLE)
            ->setUser(user: $this->user)
            ->setType(type: WidgetTypeEnum::NOTE)
            ->setPosition(position: 1);

        $this->em->persist($widget);
        $this->em->flush();

        $this->client->catchExceptions(false);
        $this->client->loginUser(user: $this->user);
        $this->expectException(\RuntimeException::class);

        $this->client->request(
            method: 'DELETE',
            uri: '/widget/delete',
            parameters: ['id' => $widget->getId()],
            server: [
                'HTTP_ACCEPT' => 'text/html',
            ],
        );
    }
}
