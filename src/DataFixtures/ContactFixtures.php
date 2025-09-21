<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Amélioration : Sujets spécialisés pour la plomberie
        $plumbingSubjects = [
            'Demande de devis pour installation complète',
            'Urgence - Fuite d\'eau à réparer',
            'Maintenance préventive chaudière',
            'Rénovation salle de bain complète',
            'Installation robinetterie cuisine',
            'Problème évacuation eaux usées',
            'Remplacement chauffe-eau',
            'Installation radiateur chauffage',
            'Réparation WC qui fuit',
            'Devis pour rénovation plomberie',
            'Installation douche à l\'italienne',
            'Problème pression eau faible',
            'Remplacement canalisation ancienne',
            'Installation compteur d\'eau',
            'Conseil choix matériel plomberie',
            'Devis pour extension réseau eau',
            'Réparation fuite sous évier',
            'Installation système récupération eau',
            'Problème chauffe-eau qui ne chauffe plus',
            'Devis pour installation piscine'
        ];

        // Amélioration : Messages plus réalistes et détaillés
        $plumbingMessages = [
            'Bonjour, j\'ai une fuite d\'eau importante dans ma cuisine. Pouvez-vous intervenir rapidement ?',
            'Je souhaite refaire entièrement ma salle de bain. Pourriez-vous me faire un devis détaillé ?',
            'Ma chaudière fait du bruit et ne chauffe plus correctement. Avez-vous un technicien disponible ?',
            'Bonjour, j\'aimerais installer un nouveau robinet de cuisine. Quel est votre tarif ?',
            'J\'ai un problème d\'évacuation dans ma douche, l\'eau ne s\'écoule plus. Urgence !',
            'Pouvez-vous me conseiller pour le remplacement de mon chauffe-eau ? Quel modèle recommandez-vous ?',
            'Je voudrais installer un radiateur dans ma chambre. Êtes-vous disponible cette semaine ?',
            'Mon WC fuit constamment, même après avoir changé le joint. Que faire ?',
            'Bonjour, j\'envisage de rénover ma plomberie. Pouvez-vous me faire une estimation ?',
            'J\'ai une pression d\'eau très faible dans toute la maison. Quelle peut être la cause ?',
            'Je souhaite installer une douche à l\'italienne. Quel est le délai de réalisation ?',
            'Mes canalisations sont très anciennes et font du bruit. Faut-il les remplacer ?',
            'Bonjour, j\'aimerais installer un compteur d\'eau individuel. C\'est possible ?',
            'Quel type de robinetterie me conseillez-vous pour une cuisine moderne ?',
            'J\'ai un projet d\'extension et j\'ai besoin d\'étendre le réseau d\'eau. Devis ?',
            'Il y a une fuite sous mon évier, l\'eau coule dans le placard. Intervention rapide ?',
            'Je voudrais installer un système de récupération d\'eau de pluie. C\'est faisable ?',
            'Mon chauffe-eau ne chauffe plus du tout. Pouvez-vous diagnostiquer le problème ?',
            'Bonjour, j\'aimerais installer une piscine. Avez-vous de l\'expérience en plomberie piscine ?',
            'Mes radiateurs ne chauffent plus uniformément. Y a-t-il un problème de circulation ?'
        ];

        for ($i = 0; $i < 80; $i++) {
            $lastName = $faker->lastName;
            $firstName = $faker->firstName;
            
            $object = (new Contact())
                // Amélioration : Email cohérent avec le nom
                ->setEmail(strtolower($firstName . '.' . $lastName . '@' . $faker->randomElement(['gmail.com', 'yahoo.fr', 'hotmail.fr', 'orange.fr', 'free.fr'])))
                ->setLastname($lastName)
                ->setFirstname($firstName)
                // Amélioration : Numéro de téléphone français réaliste
                ->setPhone($faker->numerify('0# ## ## ## ##'))
                // Amélioration : Noms d'entreprises plus réalistes
                ->setCompany($faker->randomElement([
                    'Particulier',
                    'SARL ' . $faker->lastName,
                    'Ets ' . $faker->lastName,
                    'SCI ' . $faker->lastName,
                    'Promoteur immobilier',
                    'Architecte',
                    'Maître d\'œuvre',
                    'Syndic de copropriété'
                ]))
                // Amélioration : Sujets spécialisés plomberie
                ->setSubject($faker->randomElement($plumbingSubjects))
                // Amélioration : Messages réalistes et détaillés
                ->setMessage($faker->randomElement($plumbingMessages))
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