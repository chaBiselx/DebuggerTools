<?php

namespace Test\ObjectForTest;

use Test\ObjectForTest\RoleEntity;

class UserEntity
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	public $lastName;

	/**
	 * @var string
	 */
	public $firstName;

	public function __construct()
	{
		$this->id = rand(1, 999);
	}

	/**
	 * @var array|null
	 */
	private $role;

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
