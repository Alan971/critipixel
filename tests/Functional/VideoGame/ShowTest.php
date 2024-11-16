<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    /**
     * teste la pagination
     *
     * @return void
     */
    public function testShouldShowNextPage(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.pagination .page-item.active .page-link', '1');
        self::assertSelectorTextContains('.pagination .page-item:nth-child(5) .page-link', 'Suivant');

        $this->get('/?page=2');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.pagination .page-item.active .page-link', '2');
        self::assertSelectorTextContains('.pagination .page-item:nth-child(2) .page-link', 'Précédent');
    }
}
