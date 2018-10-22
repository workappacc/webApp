<?php

namespace AdminBundle\Security\Voter;

use AdminBundle\Entity\User;
use AdminBundle\Service\RoleManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PageVoter extends Voter
{
    const POSTS = 'posts';
    const USERS = 'users';
    const FEEDBACKS = 'feedbacks';
    const MAIN = 'main';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::POSTS, self::USERS, self::FEEDBACKS, self::MAIN])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case self::POSTS:
                return $this->canViewPostsPage($token);
            case self::USERS:
                return $this->canViewUsersPage($token);
            case self::FEEDBACKS:
                return $this->canViewFeedbacksPage($token);
            case self::MAIN:
                return $this->canViewMainPage($token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canViewPostsPage($token)
    {
        if ($this->decisionManager->decide($token, [RoleManager::ROLE_MANAGER])) {
            return true;
        }

        return false;
    }

    private function canViewUsersPage($token)
    {
        if ($this->decisionManager->decide($token, [RoleManager::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    private function canViewFeedbacksPage($token)
    {
        if ($this->decisionManager->decide($token, [RoleManager::ROLE_FEEDBACK_MANAGER])) {
            return true;
        }

        return false;
    }

    private function canViewMainPage($token)
    {
        $user = $token->getUser();

        if (!$user instanceof User)
        {
            return false;
        }

        return true;
    }
}