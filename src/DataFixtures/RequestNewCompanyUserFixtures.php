<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\RequestNewCompanyUser;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RequestNewCompanyUserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer les entreprises et utilisateurs existants
        $companies = $manager->getRepository(Company::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($companies) || empty($users)) {
            return; // Pas de données à traiter
        }

        // Amélioration : Types de demandes réalistes
        $requestTypes = [
            'ROLE_PLUMBER' => 'Demande d\'ajout d\'un plombier',
            'ROLE_ACCOUNTANT' => 'Demande d\'ajout d\'un comptable',
            'ROLE_MANAGER' => 'Demande d\'ajout d\'un gestionnaire',
            'ROLE_TECHNICIAN' => 'Demande d\'ajout d\'un technicien',
            'ROLE_SALES' => 'Demande d\'ajout d\'un commercial'
        ];

        // Créer 20 demandes d'ajout d'utilisateurs
        for ($i = 0; $i < 20; $i++) {
            $company = $faker->randomElement($companies);
            $user = $faker->randomElement($users);
            $role = $faker->randomElement(array_keys($requestTypes));

            // Amélioration : Emails de demande réalistes
            $requestEmail = strtolower($faker->firstName . '.' . $faker->lastName . '@' . $faker->randomElement(['gmail.com', 'yahoo.fr', 'hotmail.fr', 'orange.fr', 'free.fr']));

            $request = new RequestNewCompanyUser();
            $request->setCompanyId($company->getId())
                ->setRole($role)
                ->setEmail($requestEmail)
                ->setUserId($user->getId());

            $manager->persist($request);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
            UserFixtures::class,
        ];
    }
}
