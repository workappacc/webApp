<?php

namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
/**
 * Role Entity
 *
 * @ORM\Entity
 * @ORM\Table( name="roles" )
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\RoleRepository")
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     */
    private $role;
    /**
     * Populate the role field
     * @param string $role ROLE_FOO etc
     */
    public function __construct( $role )
    {
        $this->role = $role;
    }
    /**
     * Return the role field.
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
    /**
     * Return the role field.
     * @return string
     */
    public function __toString()
    {
        return (string) $this->role;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}