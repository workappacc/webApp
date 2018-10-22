<?php

namespace AdminBundle\Security\Voter;

use FrontBundle\Entity\FeedBack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FeedBackVoter extends Voter
{
    const VIEW = 'view';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW])) {
            return false;
        }

        if (!$subject instanceof FeedBack) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case self::VIEW:
                return $this->canView();
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView()
    {
        return true;
    }
}