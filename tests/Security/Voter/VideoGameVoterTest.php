<?php

namespace App\Tests\Security\Voter;

use App\Security\Voter\VideoGameVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class VideoGameVoterTest extends VideoGameVoter
{
    /**
     * permet de voter à la place de la fonction de vote d'origine
     * permet d'accéder au formulaire  même si l'utilisateur n'est pas connecté
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return boolean
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true; // Toujours autoriser
    }
}
