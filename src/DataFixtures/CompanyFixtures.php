<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $object = (new Company())
            ->setName("Plumbpay")
            ->setEmail("team_plumbpay@outlook.com")
            ->setInvoiceEmail("team_plumbpay@outlook.com")
        ;
        $manager->persist($object);
        $manager->flush();

        $iMax = 50;

        for ($i = 0; $i < $iMax; $i++) {
            $street = $faker->streetName;
            $parts = explode(' ', $street, 2);
            $streetLabel = $parts[0];
            $streetName = $parts[1] ?? '';

            $companyEmail=$faker->unique()->safeEmail;

            $object = (new Company())
                ->setName($faker->company)
                ->setEmail($companyEmail)
                ->setInvoiceEmail($companyEmail)
                ->setAddressNumber(rand(1,120))
                ->setAddressType($streetLabel)
                ->setAddressName($streetName)
                ->setAddressZipCode($faker->departmentNumber())
                ->setAddressCity($faker->city)
                ->setCompanyNumber($faker->siret())
                ->setIban($faker->iban)
                ->setBic($faker->swiftBicNumber)
                ->setAddressCountry("France")
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}