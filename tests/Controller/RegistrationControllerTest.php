<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;
    private TranslatorInterface $translator;

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->translator = $container->get(TranslatorInterface::class);

        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $this->userRepository = $container->get(UserRepository::class);

        foreach ($this->userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();
    }

    public function testRegister(): void
    {
        $this->client->request(method: 'GET', uri: '/authenticate');
        self::assertResponseIsSuccessful();

        $this->client->submitForm(
            button: $this->translator->trans(id: 'label.register', domain: 'authentication'),
            fieldValues: [
                'registration_form[email]' => 'newuser@example.com',
                'registration_form[nickname]' => 'newbie',
                'registration_form[plainPassword]' => 'password123',
                'registration_form[agreeTerms]' => true,
            ]
        );

        self::assertResponseRedirects('/home');
        self::assertCount(1, $this->userRepository->findAll());
    }
}
