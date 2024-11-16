<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Doctrine\DataFixtures\TagFixtures;
use App\Doctrine\DataFixtures\UserFixtures;
use App\Doctrine\DataFixtures\VideoGameFixtures;

use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Model\Entity\Review;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Faker\Generator;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;

final class coverageTest extends TestCase
{
    /**
     * test statique des fixtures de tags
     *
     * @return void
     */
    public function testTagsFixtures(): void
    {
        // Mock du gestionnaire d'objets etle générateur Faker pour éviter de générer des mots aléatoires dans le test
        $objectManager = $this->createMock(ObjectManager::class);
        $faker = $this->createMock(Generator::class);
        // Configure the __call magic method to handle 'word' and return 'tagname'
        $faker->method('__call')
            ->with('word', [])
            ->willReturn('tagname');
        // Création de l'instance de la fixture
        $tagFixtures = new TagFixtures($faker);
        // Vérification que persist est appelé 5 fois, une pour chaque Tag
        $objectManager->expects($this->exactly(5))  // 5 tags
        ->method('persist')
        ->with($this->isInstanceOf(Tag::class));

        // Vérification que flush est appelé une seule fois à la fin
        $objectManager->expects($this->once())
        ->method('flush');

        // Appel de la méthode load() de la fixture
        $tagFixtures->load($objectManager);
    }
    /**
     * test statique des fixtures d'utilisateurs
     *
     * @return void
     */
    public function testUsersFixtures(): void
    {
        //mock
        $objectManager = $this->createMock(ObjectManager::class);

        // Vérification que persist est appelé pour chaque utilisateur (10 appels)
        $objectManager->expects($this->exactly(10))  // 10 utilisateurs à créer
            ->method('persist')
            ->with($this->isInstanceOf(User::class));  // Chaque appel doit persister un objet de type User

        // Vérification que flush est appelé une seule fois
        $objectManager->expects($this->once())
            ->method('flush');

        // Création de la fixture
        $userFixtures = new UserFixtures();
        // Appel de la méthode load() de la fixture
        $userFixtures->load($objectManager);
    }
    /**
     * test statique des fixtures de jeux vidéo
     *
     * @return void
     */
    public function testVideoGameFixtures(): void
    {
        // mock
        $objectManager = $this->createMock(ObjectManager::class);
        $faker = $this->createMock(Generator::class);
        $calculateAverageRating = $this->createMock(CalculateAverageRating::class);
        $countRatingsPerValue = $this->createMock(CountRatingsPerValue::class);

        $tagRepository = $this->createMock(ObjectRepository::class);
        $userRepository = $this->createMock(ObjectRepository::class);

        $tags = [
            $this->createMock(Tag::class),
            $this->createMock(Tag::class),
            $this->createMock(Tag::class),
            $this->createMock(Tag::class),
            $this->createMock(Tag::class)
        ];

        $users = [
            $this->createMock(User::class),
            $this->createMock(User::class),
            $this->createMock(User::class),
            $this->createMock(User::class),
            $this->createMock(User::class),
        ];

        $tagRepository->method('findAll')->willReturn($tags);
        $userRepository->method('findAll')->willReturn($users);

        // Simulation de l'appel aux repositories pour récupérer les tags et utilisateurs
        $objectManager->method('getRepository')
            ->willReturnMap([
                [Tag::class, $tagRepository],
                [User::class, $userRepository],
            ]);
        
        // Simuler Faker pour retourner des chaînes valides pour `paragraphs`
        $faker->method('__call')
            ->willReturnCallback(function ($methodName, $arguments) {
                // Si la méthode appelée est "paragraphs", simuler son comportement
                if ($methodName === 'paragraphs') {
                    return 'Some text. ';
                }
                // Ajouter une logique par défaut pour les autres méthodes si nécessaire
                return '';
            });
        // LE CODE CI DESSOUS NE FONCTIONNE PAS car le test ne fait pas la différence entre les 2 appels
        // Vérification que persist est appelé 50 fois pour les VideoGame
        // $objectManager->expects($this->exactly(50))
        //     ->method('persist')
        //     ->with($this->isInstanceOf(VideoGame::class));

        // // Vérification que persist est appelé 250 fois pour les Review
        // $objectManager->expects($this->exactly(250))
        //     ->method('persist')
        //     ->with($this->isInstanceOf(Review::class));
        
        // Vérification que flush est appelé 2 fois
        $objectManager->expects($this->exactly(2))
            ->method('flush');

        // Création de la fixture
        $videoGameFixtures = new VideoGameFixtures($faker, $calculateAverageRating, $countRatingsPerValue);
        // Appel de la méthode load() de la fixture
        $videoGameFixtures->load($objectManager);
    }


}