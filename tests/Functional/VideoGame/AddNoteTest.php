<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use App\Model\Entity\VideoGame;
use App\Security\Voter\VideoGameVoter;
use App\Tests\Security\Voter\VideoGameVoterTest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class AddNoteTest extends FunctionalTestCase
{

    /**
     * Voir un jeu vidéo
     *
     * @return void
     */
    // public function testShouldShowVideoGame(): void
    // {
    //     $this->client->request('GET', '/jeu-video-0');
    //     self::assertResponseIsSuccessful();
    //     self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    // }

    /**
     * ajout d'une note qui fonctionne
     *
     * @return void
     */
    // public function testAddNoteShouldSucceeded(): void
    // {
    //     // Remplacer le voter dans le conteneur de services
    //     $this->getContainer()->set(VideoGameVoter::class, new VideoGameVoterTest());

    //     // utiliser le module sécurity pour se logger : login programaticaly
    //     $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail('user+1@email.com');
    //     $this->client->loginUser($user,'password', ['_remember_me' => true]);
    //     // self::assertTrue($this->client->getContainer()->get('security.token_storage')->getToken() !== null);

    //     // choix d'un jeu vidéo que l'utilisateur n'a pas encore noté
    //     $videoGame = $this->videoGameChoice($user);
    //     $slug = $videoGame->getSlug();
    //     if ($slug) {
    //         echo "\n" .  'nom du jeu : ' . $slug . 'user : ' . $user->getUsername() . "\n" ;
    //         $this->get('/' . $slug );
    //     }
    //     else {
    //         throw new \Exception('No slug found, change user in the test code');
    //     }
    //     // ajout de notes de l'utilisateur
    //     $this->client->submitForm('Poster', [
    //         'review[rating]' => $rating= rand(1,5),
    //         'review[comment]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    //     ]);
    //     // controle des messages d'erreur dans le formilaire
    //     if (!$this->client->getResponse()->isSuccessful()) {
    //         $crawler = $this->client->getCrawler();
    //         $formErrors = $crawler->filter('.form-error-message')->each(function ($node) {
    //             return $node->text();
    //         });
    //         echo 'Form Errors : ';
    //         print_r( $formErrors);
    //     }
        
    //     $this->client->followRedirect();
    //     self::assertResponseIsSuccessful();
        
    //     // vérification de l'arrivée sur la nouvelle page 
    //     // ne fonctionnne pas car le test n'utilise pas les variables de session
    //     // self::assertResponseStatusCodeSame('/' . $slug);
    //     // de retour dans le controleur la session est vide car le test n'utilise pas les variables de session

    //     // vérification de l'enregistrement de la note
    //     // IMPOSSIBLE également de faire le test car le test n'utilise pas les variables de session
    //     // Le controleur n'est pas vérifié et l'enregistremenent de la note non plus
    //     // $lastReview = $this->getEntityManager()->getRepository(Review::class)->findOneBy([
    //     //     'videoGame' => $videoGame,
    //     //     'user' => $user
    //     // ]);
    //     // if (!$lastReview) {
    //     //     throw new \Exception('No review found while form was submitted');
    //     // }
    //     // self::assertSame($lastReview->getRating(), $rating, 'The rating is not the same');

    // } 

    /**
     * Ajout d'une note qui fonctionne, version simplifiée
     *
     * @return void
     */
    public function testShouldPostReview(): void
    {

        // Remplacer le voter dans le conteneur de services
        $this->getContainer()->set(VideoGameVoter::class, new VideoGameVoterTest());
        $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail('user+0@email.com');
        $this->client->loginUser($user,'password');
        // choix d'un jeu vidéo que l'utilisateur n'a pas encore noté
        $videoGame = $this->videoGameChoice($user);
        $slug = $videoGame->getSlug();
        $this->get('/' . $slug );

        $this->client->submitForm(
            'Poster',
            [
                'review[rating]' => 4,
                'review[comment]' => 'Mon commentaire',
            ]
        );
        self::assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'user+0');
        // self::assertSelectorTextContains('div.list-group-item:last-child p', 'Mon commentaire');
        // self::assertSelectorTextContains('div.list-group-item:last-child span.value', '4');
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
    // public function testAddNoteShouldFailed(array $formData): void
    // {
    //     // Remplacer le voter dans le conteneur de services
    //     $this->getContainer()->set(VideoGameVoter::class, new VideoGameVoterTest());

    //     // // connection d'un utilisateur
    //     $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail('user+7@email.com');
    //     $this->client->loginUser($user,'password');
    //     // self::assertTrue($this->client->getContainer()->get('security.token_storage')->getToken() !== null);
        
    //     // choix d'un jeu vidéo que l'utilisateur n'a pas encore noté
    //     $videoGame = $this->videoGameChoice($user);
    //     $slug = $videoGame->getSlug();
    //     if ($slug) {
    //         echo "\n" .  'Dans le test ' . $slug . ' , avant-> user : ' . $user->getUsername() . "\n" ;
    //         //$this->get('/' . $slug );
    //         $this->client->request('GET', '/' . $slug);
    //     }
    //     else {
    //         throw new \Exception('No slug found, change user in the test code');
    //     }
    //     // ajout de note de l'utilisateur
    //     $this->client->submitForm('Poster', $formData);
    //     // choix du type de contrôles
    //     if ($formData['review[rating]'] <= 5 && $formData['review[rating]'] > 0) {
    //         self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    //     }
    //     else {
    //         self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    //     }
    // }

    public static function provideInvalidFormData(): iterable
    {
        yield 'empty rating' => [self::getFormData(['review[rating]' => ''])];
        yield 'higher than 5' => [self::getFormData(['review[rating]' => 6])];
        yield 'lower than 1' => [self::getFormData(['review[rating]' => 0])];
        // Les commentaires vides sont autorisé
        yield 'empty comment' => [self::getFormData(['review[comment]' => ''])];
        // les commentaires trop longs ne sont pas rejetés
        yield 'too long comment' => [self::getFormData(['review[comment]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        '])];
    }

    public static function getFormData(array $overrideData = []): array
    {
        return array_merge([
            'review[rating]' => $rating = rand(1,5),
            'review[comment]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        ] , $overrideData);
    }

    private function isGranted(string $attribute, VideoGame $videoGame): bool
    {
        // Simulez la vérification des autorisations ici, en fonction de votre logique d'autorisation
        return $this->getContainer()->get('security.authorization_checker')->isGranted($attribute, $videoGame);
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
}
