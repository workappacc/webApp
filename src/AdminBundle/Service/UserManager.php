<?php

namespace AdminBundle\Service;

use AdminBundle\Entity\Role;
use AdminBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function __construct(EntityManagerInterface $entityManagerInterface, ContainerInterface $containerInterface)
    {
        $this->em = $entityManagerInterface;
        $this->container = $containerInterface;
    }
    
    public function getAllUsers()
    {
        return $this->em->getRepository(User::class)->getAllUsers();
    }

    public function addUser(User $user)
    {
        $role = $this->container->get('AdminBundle\Service\RoleManager')->isRoleExists('ROLE_MANAGER');
        if (!$role) {
            $role = new Role('ROLE_MANAGER');
        }

        $this->em->persist($role);

        $user->addRole($role);
        $user->setUsername($user->getUsername());
        $user->setEmail($user->getEmail());
        $password = $this->container->get('security.password_encoder')
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
        $password = $this->container->get('security.password_encoder')->encodePassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setUsername($user->getUsername());
        $user->setPassword($password);
        $user->setEmail($user->getEmail());

        $this->em->persist($user);
        $this->em->flush();
    }

    public function deleteUser($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $this->em->remove($user);
        $this->em->flush();
    }
}