<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $pwd = 'test';

        $object = (new User())
            ->setEmail('user@user.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setLastname($faker->lastName)
        ;
        $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
        $manager->persist($object);

        $object = (new User())
            ->setEmail('admin@user.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setLastname($faker->lastName)
        ;
        $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
        $manager->persist($object);

        for ($i = 0; $i < 80; $i++) {
            $object = (new User())
                ->setEmail($faker->email)
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
            ;
            $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
            $manager->persist($object);
        }

        $manager->flush();
    }
}