<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $object = (new Role())
            ->setName("Manager")
            ->setValue("ROLE_ADMIN")
        ;
        $manager->persist($object);
        $manager->flush();

        $object = (new Role())
            ->setName("Plumber")
            ->setValue("ROLE_PLUMBER")
        ;
        $manager->persist($object);
        $manager->flush();

        $object = (new Role())
            ->setName("Accountant")
            ->setValue("ROLE_ACCOUNTANT")
        ;
        $manager->persist($object);
        $manager->flush();
    }
}