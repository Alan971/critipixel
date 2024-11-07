<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use App\Model\Entity\VideoGame;
use App\Security\Voter\VideoGameVoter;
use App\Tests\Security\Voter\VideoGameVoterTest;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class AddNoteTest extends FunctionalTestCase
{

    /**
     * ajout d'une note qui fonctionne
     *
     */
    public function testAddNoteShouldSucceeded(): void
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);
        $this->login($user->getEmail());

        // choix d'un jeu vidéo que l'utilisateur n'a pas encore noté
        $videoGame = $this->videoGameChoice($user);
        $slug = $videoGame->getSlug();
        if ($slug) {
            $this->get('/' . $slug );
        }
        else {
            throw new \Exception('No slug found, change user in the test code');
        }
        // ajout de notes de l'utilisateur
        $this->client->submitForm('Poster', [
            'review[rating]' => $rating= rand(1,5),
            'review[comment]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ], 'POST');
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        
        // vérification de l'arrivée sur la nouvelle page 
        $selector = 'div.list-group-item:last-child';
        self::assertSelectorTextContains($selector .' h3', $user->getUsername());
        self::assertSelectorTextContains($selector .' p', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
        self::assertSelectorTextContains($selector .' span.value', (string)$rating);
        
        // vérification de l'enregistrement de la review
        $lastReview = $this->getEntityManager()->getRepository(Review::class)->findOneBy([
            'videoGame' => $videoGame,
            'user' => $user
        ]);
        if (!$lastReview) {
            throw new \Exception('No Review found while form was submitted');
        }
        self::assertSame($lastReview->getRating(), $rating, 'The rating is not the same');

    } 

    /**
     * @dataProvider provideInvalidFormData
     * 
     * 
     * On s'attend a ce que chaque test avec un rating inférieur à 1 ou supérieur à 5 ou null soit 
     * ne passe pas la validation du formulaire AVANT le submit du formulaire
     * ce qui signifie une erreur InvalidArgumentException qui ne peut être récupéer par le test
     * pour les tests sur le commentaires, on s'attend que le test soit passé avant le submit du formulaire
     * même s'il est vide ou trop long car il n'y a pas de limite de taille au commentaire
     */
    public function testAddNoteShouldFailed(array $formData): void
    {
        // connexion de l'utilisateur
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'user+7@email.com']);
        $this->login($user->getEmail());  

        // choix d'un jeu vidéo que l'utilisateur n'a pas encore noté
        $videoGame = $this->videoGameChoice($user);
        $slug = $videoGame->getSlug();
        if ($slug) {
            $this->client->request('GET', '/' . $slug);
        }
        else {
            throw new \Exception('No slug found, change user in the test code');
        }

        // ajout de note de l'utilisateur
        $exceptionCaught = false;
        $isContains = false;
        try {
            $this->submit('Poster', $formData);
        }
        catch (\Exception $e) {
            $exceptionCaught = true;
            $isContains = str_contains(get_class($e), 'InvalidArgumentException');
        }
        self::assertTrue($exceptionCaught);
        self::assertTrue($isContains);
    }

    public function testShouldFailedVideoGameAlreadyRated(): void
    {
        // connexion de l'utilisateur
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'user+7@email.com']);
        $this->login($user->getEmail());  

        // choix d'un jeu vidéo que l'utilisateur n'a pas encore noté
        $videoGame = $this->videoGameBadChoice($user);
        $slug = $videoGame->getSlug();
        if ($slug) {
            $this->client->request('GET', '/' . $slug);
        }
        else {
            throw new \Exception('No slug found, change user in the test code');
        }
        // ajout de note de l'utilisateur
        $exceptionCaught = false;
        try {
            $this->submit('Poster', [
                'review[rating]' => 3,
                'review[comment]' => 'Mon commentaire 2',
            ]);
        }catch (\Exception $e) {
            $exceptionCaught = true;
        }
        self::assertTrue($exceptionCaught);
    }


    public static function provideInvalidFormData(): iterable
    {
        yield 'empty rating' => [self::getFormData(['review[rating]' => ''])];
        yield 'higher than 5' => [self::getFormData(['review[rating]' => 6])];
        yield 'lower than 1' => [self::getFormData(['review[rating]' => 0])];
    }

    public static function getFormData(array $overrideData = []): array
    {
        return array_merge([
            'review[rating]' => $rating = rand(1,5),
            'review[comment]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        ] , $overrideData);
    }

    /**
     * permet de choisir un jeu vidéo à partir d'un utilisateur
     * les critères de sélection sont :
     * 1. choisir un jeu vidéo qui n'a pas encore de note de cet utilisateur
     * 2. choisir un jeu vidéo au hasard
     * 
     * @param User $user
     * @return VideoGame
     */
    private function videoGameChoice(User $user): VideoGame
    {
        $videoGames = $this->getEntityManager()->getRepository(VideoGame::class)->findAll();
        $reviews = $this->getEntityManager()->getRepository(Review::class)->findBy(['user' => $user]);
        $slug = null;
        shuffle($videoGames);
        foreach ($videoGames as $videoGame) {
            foreach ($reviews as $review) {
                if($videoGame->getId() === $review->getVideoGame()->getId()) {
                    $slug = null;
                }
                else {
                    $slug = $videoGame->getSlug();
                }
            }
            if($slug) {
                break;
            }
        }
        if ($slug) {
            return $this->getEntityManager()->getRepository(VideoGame::class)->findOneBy(['slug' => $slug]);
        }
        else {
            throw new \Exception('No slug found, change user in the test code');
        }
    }
    /**
     * permet de choisir un jeu vidéo à partir d'un utilisateur
     * le seul critère de sélection est :
     * - choisir un jeu vidéo qui a déjà une note de cet utilisateur
     * 
     * @param User $user
     * @return VideoGame
     */
    private function videoGameBadChoice(User $user): VideoGame
    {
        $videoGames = $this->getEntityManager()->getRepository(VideoGame::class)->findAll();
        $reviews = $this->getEntityManager()->getRepository(Review::class)->findBy(['user' => $user]);
        $slug = null;
        foreach ($videoGames as $videoGame) {
            foreach ($reviews as $review) {
                if($videoGame->getId() === $review->getVideoGame()->getId()) {
                    $slug = $videoGame->getSlug();;
                }
            }
            if($slug) {
                break;
            }
        }
        if ($slug) {
            return $this->getEntityManager()->getRepository(VideoGame::class)->findOneBy(['slug' => $slug]);
        }
        else {
            throw new \Exception('No slug found, change user in the test code');
        }
    }

}