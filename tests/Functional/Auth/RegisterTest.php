<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterTest extends FunctionalTestCase
{
    public function testThatRegistrationShouldSucceeded(): void
    {
        $this->get('/auth/register');

        $dataUser = self::getFormData();
        $this->client->submitForm('S\'inscrire', $dataUser);
        self::assertResponseRedirects('/auth/login');

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $dataUser['register[email]']]);

         /** @var UserPasswordHasherInterface $userPasswordHasher */
        $userPasswordHasher = $this->service(UserPasswordHasherInterface::class);

        self::assertNotNull($user);
        self::assertSame($dataUser['register[username]'], $user->getUsername());
        self::assertSame($dataUser['register[email]'], $user->getEmail());
        self::assertTrue($userPasswordHasher->isPasswordValid($user, $dataUser['register[plainPassword]']));
    }

    /**
     * @dataProvider provideInvalidFormData
     * @phpstan-param array<string, mixed> $formData
     */
    public function testThatRegistrationShouldFailed(array $formData): void
    {
        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', $formData);

        self::assertResponseIsUnprocessable();
    }

    /**
     * @phpstan-return iterable<array{0: array<string, mixed>}>
     */
    public static function provideInvalidFormData(): iterable
    {
        yield 'empty username' => [self::getFormData(['register[username]' => ''])];
        yield 'non unique username' => [self::getFormData(['register[username]' => 'user+1'])];
        yield 'too long username' => [self::getFormData(['register[username]' => 'Lorem ipsum dolor sit amet orci aliquam'])];
        yield 'empty email' => [self::getFormData(['register[email]' => ''])];
        yield 'non unique email' => [self::getFormData(['register[email]' => 'user+1@email.com'])];
        yield 'invalid email' => [self::getFormData(['register[email]' => 'fail'])];
    }

    /**
     * getFormData initialise les données de test
     *
     * @param array <string, mixed> $overrideData
     * @return array <string, mixed>
     */
    public static function getFormData(array $overrideData = []): array
    {
        return array_merge([
            'register[username]' => uniqid() .'username',
            'register[email]' => uniqid() . 'user@email.com',
            'register[plainPassword]' => 'SuperPassword123!'
        ] , $overrideData);
    }
}

