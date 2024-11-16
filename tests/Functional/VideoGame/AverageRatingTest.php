<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Rating\RatingHandler;

final class AverageRatingTest extends FunctionalTestCase
{
    public function testAverageRatingOnEachVideoGames(): void
    {
        // chargement de tous les jeux vidéo
        $videoGames = $this->getEntityManager()->getRepository(VideoGame::class)->findAll();
        // calcul de la moyenne sur la base des notes existantes 
        foreach ($videoGames as $videoGame) {
            // ...dans la table videogame
            $rating = (int) ceil(($videoGame->getNumberOfRatingsPerValue()->getNumberOfOne() + 
            2 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo() + 
            3 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree() + 
            4 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour() + 
            5 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive()
            ) / 5);
            self::assertSame($rating, $videoGame->getAverageRating());
            // ...dans la table review
            $sum = 0;
            $count = 0;
            $reviews = $videoGame->getReviews()->toArray();
            array_walk($reviews, static function( Review $review) use (&$sum, &$count) {
                $sum += $review->getRating();
                $count++;
            });
            self::assertSame((int) ceil($sum / $count), $videoGame->getAverageRating());

            // vérifier que le nombre de notes est correct en comparant avec le nombre de votants
            $countRating = $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne() + 
            $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo() + 
            $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree() + 
            $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour() + 
            $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive();
            $countvotes = $videoGame->getReviews()->count();
            self::assertSame($countRating, $countvotes);
        }
    }

    // public function testAverageRatingOnNewVideoGame(): void {
    //     //  ajout de notes. 
    //     $tags = $this->getEntityManager()->getRepository(Tag::class)->findAll();
    //     $users = $this->getEntityManager()->getRepository(User::class)->findAll();

    //     $videoGame = new VideoGame();
    //     $videoGame->setTitle('Jeu vidéo XYZ');
    //     $videoGame->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
    //     $videoGame->setReleaseDate(new \DateTimeImmutable());
    //     $videoGame->setTest('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
    //     $videoGame->setRating(rand(1,5));
    //     $videoGame->setImageName('video_game_1.png');
    //     $videoGame->setImageSize(2_098_872);
    //     $videoGame->getTags()->add($tags[rand(0,count($tags)-1)]);
    //     // création de $i votes utilisateurs
    //     shuffle($users);
    //     $i=rand(0,count($users)-1);
    //     $calculateAverageRating = new RatingHandler();
    //     foreach (array_slice($users, 0, $i) as $user) {
    //         $review = new Review();
    //         $review->setUser($user);
    //         $review->setVideoGame($videoGame);
    //         $review->setRating(rand(1,5));
    //         $review->setComment('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
    //         $videoGame->getReviews()->add($review);
    //         $this->getEntityManager()->persist($review);
    //         $calculateAverageRating->calculateAverage($videoGame);
    //         $calculateAverageRating->countRatingsPerValue($videoGame);
    //     }
    //     $this->getEntityManager()->persist($videoGame);
    //     // calcul de la moyenne sur la base des notes ajoutées
    //     // ...dans la table videogame
    //     $rating = (int) ceil(($videoGame->getNumberOfRatingsPerValue()->getNumberOfOne() + 
    //     2 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo() + 
    //     3 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree() + 
    //     4 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour() + 
    //     5 * $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive()
    //     ) / 5);
    //     echo $videoGame->getId() . ' : ' . $videoGame->getAverageRating() . ' vs ' .  $rating . "\n";
    //     self::assertSame($rating, $videoGame->getAverageRating());
    //     // ...dans la table review
    //     $sum = 0;
    //     $count = 0;
    //     $reviews = $videoGame->getReviews()->toArray();
    //     array_walk($reviews, static function( Review $review) use (&$sum, &$count) {
    //         $sum += $review->getRating();
    //         $count++;
    //     });
    //     self::assertSame((int) ceil($sum / $count), $videoGame->getAverageRating());
    //     // pas de nécéssité de flusher car c'est juste un test
    // }
}