<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Devis;
use App\Entity\Product;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuotationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $companies = $manager->getRepository(Company::class)->findAll();
        $customers = $manager->getRepository(Client::class)->findAll();
        $products = $manager->getRepository(Product::class)->findAll();

        foreach ($companies as $company) {
            for ($i = 0; $i < 9; $i++) {
                $date = new DateTime();
                $dateString = $date->format('Ymd');
                $numQuotation = substr($company->getName(),0,3) . "_" . $i . "_" . $dateString;

                $iRandProd = $faker->numberBetween(2, 5);

                $randomProducts = $faker->randomElements($products, $iRandProd);
                $arrProducts = [];
                foreach ($randomProducts as $prod){
                    $category = $prod->getCategory();
                    $arrProducts[] = [
                        'id' => $prod->getId(),
                        'name' => $prod->getName(),
                        'description' => $prod->getDescription(),
                        'price' => $prod->getPrice(),
                        'tva' => $prod->getTva(),
                        'category' => $category->getName(),
                    ];
                }

                $totalPrice = array_reduce($randomProducts, function ($total, $product) {
                    return $total + $product->getPrixTotale();
                }, 0);

                $object = (new Devis())
                    ->setClient($faker->randomElement($customers))
                    ->setNumDevis($numQuotation)
                    ->setProduits($arrProducts)
                    ->setEntreprise($company)
                    ->setTotalPrice($totalPrice)
                ;

                $manager->persist($object);
            }
        }
        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            CompanyFixtures::class,
            CustomerFixtures::class,
            ProductFixtures::class,
            CategoryFixtures::class
        ];
    }
}