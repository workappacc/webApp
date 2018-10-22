<?php

namespace AdminBundle\Security\Voter;

use AdminBundle\Entity\Post;
use AdminBundle\Entity\User;
use AdminBundle\Service\RoleManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    const ADD = 'add';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::ADD, self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Post) {
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

        /** @var Post $post */
        $post = $subject;

        switch ($attribute) {
            case self::ADD:
                return $this->canAdd($post, $user);
            case self::VIEW:
                return $this->canView($post, $user);
            case self::EDIT:
                return $this->canEdit($post, $user);
            case self::DELETE:
                return $this->canDelete($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canAdd(Post $post, User $user)
    {
        $roles = $user->getRoles();
        $role = $roles[0]->getRole();

        if (in_array($role, [RoleManager::ROLE_MANAGER, RoleManager::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    private function canView(Post $post, User $user)
    {
        $roles = $user->getRoles();
        $role = $roles[0]->getRole();

        if ($role === RoleManager::ROLE_ADMIN) {
            return true;
        }

        if ($user->getId() === $post->getUser()->getId()) {
            return true;
        }

        return false;
    }

    private function canEdit(Post $post, User $user)
    {
        $roles = $user->getRoles();
        $role = $roles[0]->getRole();

        if ($role === RoleManager::ROLE_ADMIN) {
            return true;
        }

        if ($user->getId() === $post->getUser()->getId()) {
            return true;
        }
    }

    private function canDelete(Post $post, User $user)
    {
        $roles = $user->getRoles();
        $role = $roles[0]->getRole();

        if ($role === RoleManager::ROLE_ADMIN) {
            return true;
        }

        if ($user->getId() === $post->getUser()->getId()) {
            return true;
        }
    }
}