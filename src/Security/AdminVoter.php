<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'ROLE_SUPER_ADMIN' && null === $subject;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return $token->getUser() !== null && in_array('ROLE_SUPER_ADMIN', $token->getUser()->getRoles(), true);
    }
}