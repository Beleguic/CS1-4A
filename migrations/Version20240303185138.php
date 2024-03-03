<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303185138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP CONSTRAINT fk_fe86641019eb6921');
        $this->addSql('DROP INDEX idx_fe86641019eb6921');
        $this->addSql('ALTER TABLE facture ADD client JSON NOT NULL');
        $this->addSql('ALTER TABLE facture DROP client_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE facture ADD client_id UUID NOT NULL');
        $this->addSql('ALTER TABLE facture DROP client');
        $this->addSql('COMMENT ON COLUMN facture.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT fk_fe86641019eb6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_fe86641019eb6921 ON facture (client_id)');
    }
}
