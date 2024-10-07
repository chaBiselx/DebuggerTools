<?php

namespace Test\ObjectForTest;

use Test\ObjectForTest\RoleEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 */
class UserEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
	private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
	public $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
	public $firstName;

    /**
     * @ORM\ManyToOne(targetEntity=RoleEntity::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $role;

	public function __construct()
	{
		$this->id = rand(1, 999);
	}

	public function getRole(): ?RoleEntity
	{
		return $this->role;
	}

	public function setRole(?RoleEntity $role): self
	{
		$this->role = $role;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $lastName
	 *
	 * @return UserEntity
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param string $firstName
	 *
	 * @return UserEntity
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @param $role
	 *
	 * @return bool
	 */
	public function hasRole($role)
	{
		return $role == $this->getRole()->getRoleName();
	}
}
