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
}
