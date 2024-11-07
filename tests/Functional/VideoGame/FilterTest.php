<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider provideMultipleFormData
     * 
     * 
     */
    public function testShouldFilterVideoGamesByTag(array $formData): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', [
            'filter[tags]' => $formData['filter[tags]'],
        ], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount($formData['expectedAnswer'], 'article.game-card');
    }

    /**
     * @dataProvider provideWrongFormData
     * 
     *
     */
    public function testShouldNotFilterVideoGamesByTag(array $formData): void
    {
        $data = $this->getFormData();
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $exceptionCaught = false;
        try{
            $this->client->submitForm('Filtrer', [
                'filter[tags]' => $formData['filter[tags]'],
            ], 'GET');
        }
        catch (\Exception $e) {
            $exceptionCaught = true;
        }
        self::assertTrue($exceptionCaught);
    }

    public static function getFormData(array $overrideData = []): array
    {
        return array_merge([
            'filter[tags]' => '1', 
            'expectedAnswer' =>'10',
        ] , $overrideData);
    }

    public static function provideMultipleFormData(): iterable
    {
        yield 'only one tag' => [self::getFormData(['filter[tags]' => [1], 'expectedAnswer' => 10])];
        yield 'multiple tags' => [self::getFormData(['filter[tags]' => [1, 2], 'expectedAnswer' => 0])];
    }
    public static function provideWrongFormData(): iterable
    {
        yield 'empty tag' => [self::getFormData(['filter[tags]' => ''])];
        yield 'unknown tag' => [self::getFormData(['filter[tags]' => 'abc'])];
    }
}