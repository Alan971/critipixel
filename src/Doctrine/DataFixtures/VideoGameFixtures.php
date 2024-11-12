<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

use function array_fill_callback;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $tags = $manager->getRepository(Tag::class)->findAll();

        $users = $manager->getRepository(User::class)->findAll();

        if(!is_string($this->faker->paragraphs(10, true)) || !is_string($this->faker->paragraphs(6, true))){
            throw new \Exception('Erreur de fixture');
        }

        $videoGames = array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame)
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        array_walk($videoGames, static function (VideoGame $videoGame, int $index) use ($tags) {
            $videoGame->getTags()->add($tags[$index % 5]);
        });

        array_walk($videoGames, [$manager, 'persist']);

        $manager->flush();

        array_walk($videoGames, function (VideoGame $videoGame, int $index) use ($users, $manager) {
            shuffle($users);

            foreach (array_slice($users, 0, 5) as $user) {
                if(!is_string($this->faker->paragraphs(1, true))){
                    throw new \Exception('Erreur de fixture');
                }
                $review = (new Review())
                    ->setUser($user)
                    ->setVideoGame($videoGame)
                    ->setRating($this->faker->numberBetween(1, 5))
                    ->setComment($this->faker->paragraphs(1, true))
                ;

                $videoGame->getReviews()->add($review);

                $manager->persist($review);

                $this->calculateAverageRating->calculateAverage($videoGame);
                $this->countRatingsPerValue->countRatingsPerValue($videoGame);
            }
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TagFixtures::class, UserFixtures::class];
    }
}
