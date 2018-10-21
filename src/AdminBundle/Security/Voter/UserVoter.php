<?php

namespace AdminBundle\Security\Voter;

use AdminBundle\Entity\User;
use AdminBundle\Service\RoleManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::ADD, self::EDIT, self::DELETE])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::ADD:
            case self::EDIT:
            case self::DELETE:
                return $this->canDo($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDo(User $user)
    {
        $roles = $user->getRoles();
        $role = $roles[0]->getRole();

        if ($role === RoleManager::ROLE_ADMIN) {
            return true;
        }

        return false;
    }
}