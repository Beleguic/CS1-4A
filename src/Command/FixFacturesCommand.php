<?php

namespace App\Command;

use App\Entity\Facture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fix-factures',
    description: 'Corrige les factures qui ont des produits vides',
)]
class FixFacturesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $factures = $this->entityManager->getRepository(Facture::class)->findAll();
        $fixedCount = 0;

        foreach ($factures as $facture) {
            $produits = $facture->getProduits();
            
            // Vérifier si les produits sont vides ou mal formatés
            $needsFix = false;
            if (is_array($produits)) {
                foreach ($produits as $produit) {
                    if (empty($produit) || !isset($produit['name'])) {
                        $needsFix = true;
                        break;
                    }
                }
            }

            if ($needsFix) {
                // Supprimer cette facture car elle a des données corrompues
                $this->entityManager->remove($facture);
                $fixedCount++;
                $io->writeln("Facture {$facture->getNumFacture()} supprimée (données corrompues)");
            }
        }

        $this->entityManager->flush();

        $io->success("$fixedCount factures corrompues ont été supprimées. Les nouvelles factures créées auront la bonne structure.");

        return Command::SUCCESS;
    }
}
