<?php

namespace App\Service;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRoles(): array
    {
        $roles = $this->entityManager->getRepository(Role::class)->findAll();

        $roleNames = [];
        foreach ($roles as $role) {
            $roleNames[$role->getName()] = $role->getValue();
        }

        return $roleNames;
    }
}
