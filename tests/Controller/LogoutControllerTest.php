<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LogoutControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);

        $em = $container->get('doctrine.orm.entity_manager');
        foreach ($this->userRepository->findAll() as $u) {
            $em->remove($u);
        }

        $user = (new User())
            ->setEmail('test-logout@example.com')
            ->setNickname('LogoutTester')
            ->setPassword('password');

        $em->persist($user);
        $em->flush();
    }

    public function testLogoutRedirectsToAuth(): void
    {
        $testUser = $this->userRepository->findOneByEmail('test-logout@example.com');
        self::assertInstanceOf(User::class, $testUser);
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/logout');

        self::assertResponseRedirects('/authenticate', Response::HTTP_SEE_OTHER);
        $this->client->followRedirect();
    }
}
