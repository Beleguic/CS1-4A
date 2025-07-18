<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:seed-french-data',
    description: 'Charge les données de test françaises pour PlumbPay',
)]
class SeedFrenchDataCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force l\'exécution sans confirmation')
            ->setHelp('Cette commande charge les données de test françaises en utilisant les fixtures Doctrine.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🇫🇷 Chargement des données de test françaises pour PlumbPay');

        // Vérification de l'environnement
        $env = $_ENV['APP_ENV'] ?? 'dev';
        if ($env === 'prod' && !$input->getOption('force')) {
            $io->error('❌ Cette commande ne peut pas être exécutée en environnement de production sans --force');
            return Command::FAILURE;
        }

        if (!$input->getOption('force')) {
            $io->warning([
                'Cette commande va SUPPRIMER TOUTES LES DONNÉES existantes',
                'et charger des données de test françaises dans votre base de données.',
                'Environnement détecté : ' . $env,
                '',
                '⚠️  ATTENTION : Toutes les données actuelles seront perdues !',
            ]);

            if (!$io->confirm('Voulez-vous continuer et SUPPRIMER toutes les données ?', false)) {
                $io->info('Opération annulée.');
                return Command::SUCCESS;
            }
        }

        $io->section('🗑️  Suppression de toutes les données existantes...');
        $io->text('Purge complète de la base de données en cours...');

        // Exécuter la commande doctrine:fixtures:load avec purge complète
        $process = new Process([
            'php', 
            'bin/console', 
            'doctrine:fixtures:load', 
            '--group=FrenchSeeder',
            '--purge-with-truncate',  // Purge plus rapide et complète
            '--no-interaction'
        ]);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $io->success([
                '✅ Base de données réinitialisée et données de test françaises chargées avec succès !',
                '',
                '🗑️  Toutes les données précédentes ont été supprimées',
                '',
                '📊 Nouvelles données créées :',
                '• 1 Super Administrateur',
                '• 3 Entreprises françaises de plomberie',
                '• 36 Utilisateurs (12 par entreprise : 1 admin, 1 comptable, 10 plombiers)',
                '• 18 Catégories (6 par entreprise)',
                '• 60 Produits de plomberie (20 par entreprise)',
                '• 75 Clients (25 par entreprise)',
                '• 45 Devis (15 par entreprise)',
                '• 30 Factures (10 par entreprise)',
                '',
                '📧 Email Super Admin : ' . $_ENV['SUPER_ADMIN_EMAIL'],
                '🔐 Mot de passe générique : ' . $_ENV['GENERIC_PASSWORD'],
            ]);

            $io->note([
                'Utilisez ces données pour tester votre application.',
                'Les mots de passe sont hashés différemment pour chaque utilisateur.',
                'Vérifiez les logs pour voir les entreprises créées.',
            ]);

            return Command::SUCCESS;
        } else {
            $io->error([
                '❌ Erreur lors du chargement des données !',
                'Code de sortie : ' . $process->getExitCode(),
                'Erreur : ' . $process->getErrorOutput(),
            ]);

            return Command::FAILURE;
        }
    }
} 