<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231215212710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture ADD devis_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE facture ADD date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE facture ADD amount NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE facture ADD paid BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641041DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FE86641041DEFADA ON facture (devis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE facture DROP CONSTRAINT FK_FE86641041DEFADA');
        $this->addSql('DROP INDEX IDX_FE86641041DEFADA');
        $this->addSql('ALTER TABLE facture DROP devis_id');
        $this->addSql('ALTER TABLE facture DROP date');
        $this->addSql('ALTER TABLE facture DROP amount');
        $this->addSql('ALTER TABLE facture DROP paid');
    }
}
