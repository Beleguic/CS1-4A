<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 80; $i++) {
            $object = (new Contact())
                ->setEmail($faker->email)
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setPhone($faker->phoneNumber)
                ->setCompany($faker->company)
                ->setSubject($faker->text(15))
                ->setMessage($faker->text(100))
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}