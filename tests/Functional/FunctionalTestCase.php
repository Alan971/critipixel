<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }
    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        if(!$this->service(EntityManagerInterface::class) instanceof EntityManagerInterface){
            throw new \Exception('class EntityManagerInterface attendue');
        }
        return $this->service(EntityManagerInterface::class);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @template T
     */
    protected function service(string $id): object
    {
        return $this->client->getContainer()->get($id);
    }

    /**
     * fonction get qui permet de faire une requête GET
     *
     * @param string $uri
     * @param array<string, mixed> $parameters
     * @return Crawler
     */
    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    protected function login(string $email = 'user+0@email.com'): void
    {
        $entityManager = $this->service(EntityManagerInterface::class);

        // Vérification explicite du type
        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \Exception('Le service retourné n\'est pas une instance d\'EntityManagerInterface.');
        }
        $user = $entityManager->getRepository(User::class)->findOneBy(['email'=> $email]);
        if($user !== null){
            $this->client->loginUser($user);
        }
        self::assertNotEmpty($user);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function submit(string $button, array $data = [], string $method = 'POST'): Crawler
    {
        return $this->client->submitForm($button, $data, $method);
    }
}
