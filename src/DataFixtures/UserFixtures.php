<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $pwd = 'test';

        $object = (new User())
            ->setEmail('user@user.fr')
            ->setLastname($faker->lastName)
            ->setFirstname($faker->firstName)
            ->setVerifiedAt(new DateTimeImmutable())
            ->setEnabled(true)
        ;
        $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
        $manager->persist($object);

        $companies = $manager->getRepository(Company::class)->findAll();
        $rolesArray = ['ROLE_ACCOUNTANT', 'ROLE_PLUMBER'];

        foreach ($companies as $company) {
            $firstUserForCompany = true;

            if ($company->getName() !== 'Plumbpay') {
                for ($i = 0; $i < 5; $i++) {
                    $randomIndex = array_rand($rolesArray);
                    $randRole = $rolesArray[$randomIndex];

                    if ($firstUserForCompany) {
                        $randRole = 'ROLE_ADMIN';
                        $firstUserForCompany = false;
                    }

                    $object = (new User())
                        ->setEmail($faker->email)
                        ->setLastname($faker->lastName)
                        ->setFirstname($faker->firstName)
                        ->setVerifiedAt(new DateTimeImmutable())
                        ->setEnabled(true)
                        ->setCompanyId($company->getId())
                        ->setCompany($company)
                        ->setRoles([$randRole])
                    ;

                    $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
                    $manager->persist($object);
                }
            } else {
                $object = (new User())
                    ->setEmail('super@user.fr')
                    ->setRoles(['ROLE_SUPER_ADMIN'])
                    ->setLastname($faker->lastName)
                    ->setFirstname($faker->firstName)
                    ->setVerifiedAt(new DateTimeImmutable())
                    ->setEnabled(true)
                    ->setCompanyId($company->getId())
                    ->setCompany($company)
                ;
                $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
                $manager->persist($object);

                $randomCompanyIndex = array_rand($companies);
                $company = $companies[$randomCompanyIndex];

                $object = (new User())
                    ->setEmail('admin@user.fr')
                    ->setRoles(['ROLE_ADMIN'])
                    ->setLastname($faker->lastName)
                    ->setFirstname($faker->firstName)
                    ->setVerifiedAt(new DateTimeImmutable())
                    ->setEnabled(true)
                    ->setCompanyId($company->getId())
                    ->setCompany($company)
                ;
                $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
                $manager->persist($object);

                $object = (new User())
                    ->setEmail('accountant@user.fr')
                    ->setRoles(['ROLE_ACCOUNTANT'])
                    ->setLastname($faker->lastName)
                    ->setFirstname($faker->firstName)
                    ->setVerifiedAt(new DateTimeImmutable())
                    ->setEnabled(true)
                    ->setCompanyId($company->getId())
                    ->setCompany($company)
                ;
                $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
                $manager->persist($object);

                $object = (new User())
                    ->setEmail('plumber@user.fr')
                    ->setRoles(['ROLE_PLUMBER'])
                    ->setLastname($faker->lastName)
                    ->setFirstname($faker->firstName)
                    ->setVerifiedAt(new DateTimeImmutable())
                    ->setEnabled(true)
                    ->setCompanyId($company->getId())
                    ->setCompany($company)
                ;
                $object->setPassword($this->passwordHasher->hashPassword($object, $pwd));
                $manager->persist($object);
            }
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