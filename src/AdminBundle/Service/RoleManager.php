<?php
declare(strict_types=1);

namespace AdminBundle\Service;

use AdminBundle\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class RoleManager
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const DEFAULT_USER_ROLE = 'ROLE_MANAGER';
    const ROLE_FEEDBACK_MANAGER = 'ROLE_FEEDBACK_MANAGER';
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

    public function getAllRoles()
    {
        return [
            'roles' => [
                'Admin' => 'ROLE_ADMIN',
                'Manager' => 'ROLE_MANAGER',
                'Feedback Manager' => 'ROLE_FEEDBACK_MANAGER',
            ]
        ];
    }
}