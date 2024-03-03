<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $companies = $manager->getRepository(Company::class)->findAll();

        $iMax = 50;
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < $iMax; $i++) {
            $object = (new Client())
                ->setNom($faker->lastName)
                ->setPrenom($faker->firstName)
                ->setEmail($faker->email)
                ->setNumeroTelephone($faker->phoneNumber)
                ->setCompanyId($companies[array_rand($companies)]->getId())
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}