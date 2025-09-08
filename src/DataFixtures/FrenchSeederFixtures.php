<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Product;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class FrenchSeederFixtures extends Fixture implements FixtureGroupInterface
{
    private array $companies = [];
    private array $categories = [];
    private array $clients = [];
    private array $products = [];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly LoggerInterface $logger
    ) {}

    public static function getGroups(): array
    {
        return ['FrenchSeeder'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $genericPassword = $_ENV['GENERIC_PASSWORD'] ?? 'MotDePasse123!';
        $superAdminEmail = $_ENV['SUPER_ADMIN_EMAIL'] ?? 'superadmin@plumbpay.fr';

        // Log des variables d'environnement pour debugging
        $this->logger->info("🔐 Mot de passe générique utilisé : {$genericPassword}");
        $this->logger->info("📧 Email Super Admin utilisé : {$superAdminEmail}");

        // 1. Créer le Super Admin
        $this->createSuperAdmin($manager, $faker, $superAdminEmail, $genericPassword);

        // 2. Créer 3 entreprises françaises
        $this->createCompanies($manager, $faker);

        // 3. Pour chaque entreprise, créer les utilisateurs, catégories, produits, clients, devis et factures
        foreach ($this->companies as $index => $company) {
            $this->createUsersForCompany($manager, $faker, $company, $genericPassword);
            $this->createCategoriesForCompany($manager, $faker, $company);
            $this->createProductsForCompany($manager, $faker, $company);
            $this->createClientsForCompany($manager, $faker, $company);
            $this->createQuotationsForCompany($manager, $faker, $company);
            $this->createInvoicesForCompany($manager, $faker, $company);
            
            $this->logger->info("L'entreprise {$company->getName()} a été créée avec succès");
        }

        $manager->flush();
        $this->logger->info("Toutes les données de test ont été créées avec succès !");
    }

    private function createSuperAdmin(ObjectManager $manager, $faker, string $superAdminEmail, string $genericPassword): void
    {
        $superAdmin = new User();
        $superAdmin->setEmail($superAdminEmail)
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setVerifiedAt(new DateTimeImmutable())
            ->setEnabled(true);

        $hashedPassword = $this->passwordHasher->hashPassword($superAdmin, $genericPassword);
        $superAdmin->setPassword($hashedPassword);

        $manager->persist($superAdmin);
    }

    private function createCompanies(ObjectManager $manager, $faker): void
    {
        $frenchCompanyNames = [
            'Plomberie Dupont SARL',
            'Ets Martin Chauffage',
            'Dubois Sanitaire & Cie'
        ];

        $frenchCities = ['Paris', 'Lyon', 'Marseille'];

        foreach ($frenchCompanyNames as $index => $companyName) {
            $company = new Company();
            $company->setName($companyName)
                ->setEmail(strtolower(str_replace([' ', 'É', 'è', '&'], ['', 'e', 'e', 'et'], $companyName)) . '@entreprise.fr')
                ->setInvoiceEmail('facturation@' . strtolower(str_replace([' ', 'É', 'è', '&'], ['', 'e', 'e', 'et'], $companyName)) . '.fr')
                ->setAddressNumber($faker->buildingNumber)
                ->setAddressType($faker->randomElement(['rue', 'avenue', 'boulevard', 'place']))
                ->setAddressName($faker->streetName)
                ->setAddressZipCode($faker->postcode)
                ->setAddressCity($frenchCities[$index])
                ->setAddressCountry('France')
                ->setCompanyNumber($faker->numerify('##########'))
                ->setIban($faker->iban('FR'))
                ->setBic($faker->swiftBicNumber);

            $manager->persist($company);
            $this->companies[] = $company;
        }
    }

    private function createUsersForCompany(ObjectManager $manager, $faker, Company $company, string $genericPassword): void
    {
        $roles = [
            ['role' => ['ROLE_ADMIN'], 'count' => 1, 'suffix' => '_admin'],
            ['role' => ['ROLE_ACCOUNTANT'], 'count' => 1, 'suffix' => '_comptable'],
            ['role' => ['ROLE_PLUMBER'], 'count' => 10, 'suffix' => '_plombier']
        ];

        $userCount = 0;
        foreach ($roles as $roleConfig) {
            for ($i = 0; $i < $roleConfig['count']; $i++) {
                $user = new User();
                $lastName = $faker->lastName;
                $firstName = $faker->firstName;
                
                $user->setEmail(strtolower($firstName . '.' . $lastName . '@' . str_replace(' ', '', $company->getName())) . '.fr')
                    ->setFirstname($firstName)
                    ->setLastname($lastName)
                    ->setRoles($roleConfig['role'])
                    ->setVerifiedAt(new DateTimeImmutable())
                    ->setEnabled(true)
                    ->setCompany($company);

                $hashedPassword = $this->passwordHasher->hashPassword($user, $genericPassword);
                $user->setPassword($hashedPassword);

                $manager->persist($user);
                $userCount++;
            }
        }
    }

    private function createCategoriesForCompany(ObjectManager $manager, $faker, Company $company): void
    {
        $plumbingCategories = [
            'Robinetterie' => 'Robinets, mélangeurs, mitigeurs pour cuisine et salle de bain',
            'Chauffage' => 'Radiateurs, chaudières, pompes à chaleur et accessoires',
            'Évacuation' => 'Canalisations, siphons, regards et systèmes d\'évacuation',
            'Sanitaire' => 'WC, lavabos, douches, baignoires et accessoires',
            'Outillage' => 'Outils spécialisés pour la plomberie et le chauffage',
            'Isolation' => 'Matériaux d\'isolation pour canalisations et réservoirs'
        ];

        foreach ($plumbingCategories as $name => $description) {
            $category = new Category();
            $category->setName($name)
                ->setDescription($description)
                ->setCompanyId($company->getId());

            $manager->persist($category);
            $this->categories[(string)$company->getId()][] = $category;
        }
    }

    private function createProductsForCompany(ObjectManager $manager, $faker, Company $company): void
    {
        $plumbingProducts = [
            // Robinetterie
            ['Mitigeur lavabo chromé', 'Mitigeur monocommande pour lavabo, finition chromée', [89, 125, 156], 20.0],
            ['Robinet évier bec mobile', 'Robinet d\'évier avec bec orientable, garantie 5 ans', [112, 145, 178], 20.0],
            ['Mitigeur douche thermostatique', 'Mitigeur thermostatique sécurité anti-brûlure', [198, 245, 289], 20.0],
            
            // Chauffage  
            ['Radiateur acier panneau', 'Radiateur acier type 22, différentes dimensions', [145, 234, 367], 20.0],
            ['Chaudière gaz condensation', 'Chaudière murale gaz condensation 24kW', [1450, 1890, 2340], 20.0],
            ['Pompe à chaleur air/eau', 'PAC air/eau réversible 8kW monophasée', [3450, 4200, 5100], 20.0],
            
            // Évacuation
            ['Tube PVC évacuation Ø100', 'Tube PVC blanc longueur 3m pour évacuation', [12, 18, 24], 20.0],
            ['Regard béton Ø315', 'Regard de visite béton avec tampon fonte', [89, 124, 156], 20.0],
            ['Siphon de sol inox', 'Siphon de sol carré inoxydable grille réglable', [45, 67, 89], 20.0],
            
            // Sanitaire
            ['WC suspendu compact', 'WC suspendu avec abattant frein de chute', [234, 289, 356], 20.0],
            ['Lavabo suspendu 60cm', 'Lavabo suspendu porcelaine blanche avec percement', [156, 198, 245], 20.0],
            ['Receveur douche extra-plat', 'Receveur acrylique 80x80cm hauteur 25mm', [167, 212, 267], 20.0],
            
            // Outillage
            ['Poste à souder gaz', 'Ensemble soudage gaz avec détendeurs et tuyaux', [289, 367, 445], 20.0],
            ['Coupe-tube à cliquet', 'Coupe-tube professionnel Ø6-67mm', [67, 89, 112], 20.0],
            ['Pince à sertir multicouche', 'Pince à sertir pour tube multicouche Ø16-32mm', [234, 289, 356], 20.0],
            
            // Isolation
            ['Mousse polyuréthane', 'Mousse PU expansive pour isolation canalisations', [8, 12, 16], 20.0],
            ['Gaine isolante Ø22', 'Gaine isolante préfendue épaisseur 9mm', [3, 5, 7], 20.0],
            ['Coquille laine de roche', 'Coquille isolante haute température Ø15-108mm', [5, 8, 12], 20.0]
        ];

        $categories = $this->categories[(string)$company->getId()];
        
        for ($i = 0; $i < 20; $i++) {
            $productData = $faker->randomElement($plumbingProducts);
            $category = $faker->randomElement($categories);
            
            $product = new Product();
            $product->setName($productData[0])
                ->setDescription($productData[1])
                ->setPrice($faker->randomElement($productData[2]) * 100) // Prix en centimes
                ->setTva($productData[3])
                ->setCategory($category)
                ->setQuantite($faker->numberBetween(1, 100))
                ->setCompanyId($company->getId());
                
            $product->setPrixTotale($product->getPrice() * $product->getQuantite());

            $manager->persist($product);
            $this->products[(string)$company->getId()][] = $product;
        }
    }

    private function createClientsForCompany(ObjectManager $manager, $faker, Company $company): void
    {
        for ($i = 0; $i < 25; $i++) {
            $client = new Client();
            $client->setNom($faker->lastName)
                ->setPrenom($faker->firstName)
                ->setEmail($faker->email)
                ->setNumeroTelephone($faker->phoneNumber)
                ->setAddressNumber($faker->buildingNumber)
                ->setAddressType($faker->randomElement(['rue', 'avenue', 'boulevard', 'place']))
                ->setAddressName($faker->streetName)
                ->setAddressZipCode($faker->postcode)
                ->setAddressCity($faker->city)
                ->setAddressCountry('France')
                ->setCompanyId($company->getId());

            $manager->persist($client);
            $this->clients[(string)$company->getId()][] = $client;
        }
    }

    private function createQuotationsForCompany(ObjectManager $manager, $faker, Company $company): void
    {
        $clients = $this->clients[(string)$company->getId()];
        $products = $this->products[(string)$company->getId()];
        
        for ($i = 0; $i < 15; $i++) {
            $client = $faker->randomElement($clients);
            $selectedProducts = $faker->randomElements($products, $faker->numberBetween(1, 5));
            
            $productsData = [];
            $totalPrice = 0;
            
            foreach ($selectedProducts as $product) {
                $quantity = $faker->numberBetween(1, 3);
                $unitPrice = $product->getPrice() / 100; // Conversion centimes en euros
                $productTotal = $quantity * $unitPrice;
                $totalPrice += $productTotal;
                
                $productsData[] = [
                    'id' => (string) $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'tva' => $product->getTva(),
                    'quantite' => $quantity,
                    'prix_totale' => $productTotal * 100, // Conversion en centimes
                    'category' => [
                        'id' => (string) $product->getCategory()->getId(),
                        'name' => $product->getCategory()->getName()
                    ]
                ];
            }
            
            $devis = new Devis();
            $devis->setClient($client)
                ->setNumDevis('DEV-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT))
                ->setMessage($faker->sentence(12))
                ->setTotalPrice($totalPrice * 100) // Conversion en centimes
                ->setProduits($productsData)
                ->setCompanyId($company->getId());

            $manager->persist($devis);
        }
    }

    private function createInvoicesForCompany(ObjectManager $manager, $faker, Company $company): void
    {
        $clients = $this->clients[(string)$company->getId()];
        $products = $this->products[(string)$company->getId()];
        
        for ($i = 0; $i < 10; $i++) {
            $client = $faker->randomElement($clients);
            $selectedProducts = $faker->randomElements($products, $faker->numberBetween(1, 4));
            
            $productsData = [];
            $totalPrice = 0;
            
            foreach ($selectedProducts as $product) {
                $quantity = $faker->numberBetween(1, 2);
                $unitPrice = $product->getPrice() / 100; // Conversion centimes en euros
                $productTotal = $quantity * $unitPrice;
                $totalPrice += $productTotal;
                
                $productsData[] = [
                    'id' => (string) $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'tva' => $product->getTva(),
                    'quantite' => $quantity,
                    'prix_totale' => $productTotal * 100, // Conversion en centimes
                    'category' => [
                        'id' => (string) $product->getCategory()->getId(),
                        'name' => $product->getCategory()->getName()
                    ]
                ];
            }
            
            $isPaid = $faker->boolean(70); // 70% des factures sont payées
            $paidAmount = $isPaid ? $totalPrice : $faker->randomFloat(2, 0, $totalPrice);
            
            $clientData = [
                'id' => (string) $client->getId(),
                'nom' => $client->getNom(),
                'prenom' => $client->getPrenom(),
                'email' => $client->getEmail(),
                'telephone' => $client->getNumeroTelephone(),
                'adresse' => $client->getAddressNumber() . ' ' . $client->getAddressType() . ' ' . $client->getAddressName(),
                'code_postal' => $client->getAddressZipCode(),
                'ville' => $client->getAddressCity(),
                'pays' => $client->getAddressCountry()
            ];
            
            $facture = new Facture();
            $facture->setNumFacture('FAC-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT))
                ->setNumDevis('DEV-' . date('Y') . '-' . str_pad($faker->numberBetween(1, 15), 4, '0', STR_PAD_LEFT))
                ->setPaid($isPaid)
                ->setDateFacture(new \DateTime($faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d')))
                ->setCompany($company->getId())
                ->setCompanyId($company->getId())
                ->setPrixTotal($totalPrice)
                ->setPrixPaye($paidAmount)
                ->setReduction($faker->numberBetween(0, 10))
                ->setProduits($productsData)
                ->setClient($clientData);

            $manager->persist($facture);
        }
    }
} 