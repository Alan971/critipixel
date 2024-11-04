<?php

namespace App\Tests\Security\Voter;

use App\Security\Voter\VideoGameVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class VideoGameVoterTest extends VideoGameVoter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true; // Toujours autoriser
    }
}
