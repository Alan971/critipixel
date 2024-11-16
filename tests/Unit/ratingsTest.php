<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use Doctrine\Persistence\ObjectManager;
use App\Rating\RatingHandler;

final class ratingsTest extends TestCase
{
    private RatingHandler $calculateAverageRating;

    protected function setUp(): void
    {
        $this->calculateAverageRating = new RatingHandler();
    }
    /**
     * test statique des ratings quand il n'y a pas de review
     *
     * @return void
     */
    public function testRatingsWhenNoReview(): void
    {
        $videoGame = $this->createMock(VideoGame::class);
        $this->calculateAverageRating->calculateAverage($videoGame);
        self::assertNull($videoGame->getAverageRating());
    }
    /**
     * test statique des ratings quand il n'y a pas de review
     *
     * @return void
     */
    public function testnumberRatingsWhenNoReview(): void
    {
        $videoGame = new VideoGame();
        $this->calculateAverageRating->countRatingsPerValue($videoGame);
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }
    /**
     * test statique des ratings quand il y a des review
     *
     * @return void
     */
    public function testRatingsWhenReview(): void
    {
        $videoGame = new VideoGame();

        $review1 = new Review();
        $review2 = new Review();
        $review3 = new Review();

        $review1->setRating(1);
        $review2->setRating(3);
        $review3->setRating(5);
        $videoGame->getReviews()->add($review1);
        $videoGame->getReviews()->add($review2);
        $videoGame->getReviews()->add($review3);

        $this->assertEquals(3, count($videoGame->getReviews()));
        $this->calculateAverageRating->calculateAverage($videoGame);
        $this->assertEquals(3, $videoGame->getAverageRating());
    }
    /**
     * test statique des ratings quand il y a des review
     *
     * @return void
     */
    public function testnumberRatingsWhenReview(): void
    {
        $videoGame = new VideoGame();

        $review1 = new Review();
        $review2 = new Review();
        $review3 = new Review();
        $review4 = new Review();
        $review5 = new Review();

        $review1->setRating(1);
        $review2->setRating(2);
        $review3->setRating(3);
        $review4->setRating(4);
        $review5->setRating(5);
        $videoGame->getReviews()->add($review1);
        $videoGame->getReviews()->add($review2);
        $videoGame->getReviews()->add($review3);
        $videoGame->getReviews()->add($review4);
        $videoGame->getReviews()->add($review5);

        $this->assertEquals(5, count($videoGame->getReviews()));
        $this->calculateAverageRating->countRatingsPerValue($videoGame);
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }
}