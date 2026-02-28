<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeControllerTest extends WebTestCase
{
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

    public function testIndex(): void
    {
        $user = $this->container->get(UserRepository::class)->findOneByEmail('me@example.com');
        self::assertNotNull($user);

        $this->client->loginUser(user: $user);
        $this->client->request(method: 'GET', uri: '/home');
        self::assertResponseIsSuccessful();
    }
}
