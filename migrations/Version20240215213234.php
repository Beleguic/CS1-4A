<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215213234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649979b1ad6');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP INDEX idx_8d93d649979b1ad6');
        $this->addSql('ALTER TABLE "user" ADD activation_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" DROP company_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE company (id UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, invoice_email VARCHAR(255) DEFAULT NULL, address_number VARCHAR(255) DEFAULT NULL, address_type VARCHAR(255) DEFAULT NULL, address_name VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, company_number VARCHAR(20) DEFAULT NULL, iban VARCHAR(34) DEFAULT NULL, bic VARCHAR(11) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN company.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "user" ADD company_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" DROP activation_token');
        $this->addSql('COMMENT ON COLUMN "user".company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8d93d649979b1ad6 ON "user" (company_id)');
    }
}
