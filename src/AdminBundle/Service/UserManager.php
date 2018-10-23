<?php

namespace AdminBundle\Service;

use AdminBundle\Entity\Role;
use AdminBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var RoleManager $roleManager
     */
    private $roleManager;

    /**
     * @var UserPasswordEncoderInterface $encoderInterface
     */
    private $encoderInterface;
    

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        RoleManager $roleManager,
        UserPasswordEncoderInterface $encoderInterface
    
    ) {
        $this->em = $entityManagerInterface;
        $this->roleManager = $roleManager;
        $this->encoderInterface = $encoderInterface;
    }
    
    public function getAllUsers()
    {
        return $this->em->getRepository(User::class)->getAllUsers();
    }

    public function addUser(User $user)
    {

        $role = $this->roleManager->isRoleExists($user->getActiveRole());
        if (!$role) {
            $role = new Role($user->getActiveRole());
        }

        $this->em->persist($role);

        $user->addRole($role);
        $password = $this->encoderInterface
            ->encodePassword(
                $user,
                $user->getPlainPassword()
            );
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function editUser(User $user)
    {
        $password = $this->encoderInterface->encodePassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function deleteUser(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}