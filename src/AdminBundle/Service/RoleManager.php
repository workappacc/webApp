<?php

namespace AdminBundle\Service;

use AdminBundle\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class RoleManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * RoleManager constructor.
     * @param EntityManagerInterface $entityManagerInterface
     */
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    /**
     * Check if role exists
     *
     * @param string $role
     * @return null|object
     */
    public function isRoleExists(string $role)
    {
        return $this->em->getRepository(Role::class)->findOneBy(['role' => $role]);
    }
}