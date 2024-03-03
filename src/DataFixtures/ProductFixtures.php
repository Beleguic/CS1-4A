<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $companies = $manager->getRepository(Company::class)->findAll();

        $faker = Factory::create('fr_FR');
        $iMax = 50;

        for ($i = 0; $i < $iMax; $i++) {
            $category = $this->getReference('category_' . $i);
            $randSentence= rand(10, 30);
            $randPrice=rand(0, 200);
            $total = $randPrice + ($randPrice * 0.2);

            $object = (new Product())
                ->setCategory($category)
                ->setName($faker->word)
                ->setDescription($faker->sentence($randSentence))
                ->setPrice($randPrice)
                ->setTva(0.20)
                ->setPrixTotale($total)
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
            CategoryFixtures::class,
        ];
    }
}