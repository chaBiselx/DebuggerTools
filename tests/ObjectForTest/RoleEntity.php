<?php

namespace Test\ObjectForTest;


class RoleEntity
{
    private $id;

    /**
     * @var string
     */
    private $roleName;

    public function __construct()
    {
        $this->permissions = [];
    }

    public function __toString()
    {
        return $this->getRoleName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): self
    {
        $this->roleName = $roleName;

        return $this;
    }
}
