<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250921200457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products ADD devis_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN products.devis_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A41DEFADA FOREIGN KEY (devis_id) REFERENCES quotations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A41DEFADA ON products (devis_id)');
        $this->addSql('ALTER TABLE quotations DROP produits');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quotations ADD produits JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A41DEFADA');
        $this->addSql('DROP INDEX IDX_B3BA5A5A41DEFADA');
        $this->addSql('ALTER TABLE products DROP devis_id');
    }
}
