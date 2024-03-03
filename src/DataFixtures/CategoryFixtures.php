<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $iMax = 50;

        for ($i = 0; $i < $iMax; $i++) {
            $randSentence= rand(10, 30);
            $object = (new Category())
                ->setDescription($faker->sentence($randSentence))
                ->setName($faker->word)
            ;

            $manager->persist($object);

            $this->addReference('category_' . $i, $object);
        }

        $manager->flush();
    }
}