<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Amélioration : Rôles système complets pour l'application
        $systemRoles = [
            ['name' => 'Super Administrateur', 'value' => 'ROLE_SUPER_ADMIN'],
            ['name' => 'Administrateur', 'value' => 'ROLE_ADMIN'],
            ['name' => 'Comptable', 'value' => 'ROLE_ACCOUNTANT'],
            ['name' => 'Plombier', 'value' => 'ROLE_PLUMBER'],
            ['name' => 'Utilisateur', 'value' => 'ROLE_USER'],
            ['name' => 'Modérateur', 'value' => 'ROLE_MODERATOR'],
            ['name' => 'Gestionnaire', 'value' => 'ROLE_MANAGER'],
            ['name' => 'Technicien', 'value' => 'ROLE_TECHNICIAN'],
            ['name' => 'Commercial', 'value' => 'ROLE_SALES'],
            ['name' => 'Support Client', 'value' => 'ROLE_SUPPORT']
        ];

        foreach ($systemRoles as $roleData) {
            $role = new Role();
            $role->setName($roleData['name'])
                ->setValue($roleData['value']);

            $manager->persist($role);
        }

        $manager->flush();
    }
}