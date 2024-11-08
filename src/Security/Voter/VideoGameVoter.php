<?php

namespace App\Security\Voter;

use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @phpstan-extends Voter<string, VideoGame>
 */
class VideoGameVoter extends Voter 
{
    public const REVIEW = 'review';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return boolean
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::REVIEW && $subject instanceof VideoGame;
    }

    /**     
     * @param string $attribute
     * @param VideoGame $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return !$subject->hasAlreadyReview($user);
    }
}
