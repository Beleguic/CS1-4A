<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716202848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('CREATE TABLE categories (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN categories.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN categories.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invoices (id UUID NOT NULL, num_facture VARCHAR(255) NOT NULL, num_devis VARCHAR(255) NOT NULL, paid BOOLEAN NOT NULL, date_facture TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, company UUID DEFAULT NULL, prix_total DOUBLE PRECISION NOT NULL, prix_paye DOUBLE PRECISION NOT NULL, reduction INT DEFAULT NULL, produits JSON NOT NULL, date_echeance TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, client JSON NOT NULL, messages VARCHAR(255) DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN invoices.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoices.company IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoices.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE products (id UUID NOT NULL, category_id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price INT NOT NULL, tva DOUBLE PRECISION NOT NULL, quantite INT DEFAULT NULL, prix_totale DOUBLE PRECISION DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
        $this->addSql('COMMENT ON COLUMN products.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN products.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN products.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quotations (id UUID NOT NULL, client_id UUID NOT NULL, message TEXT DEFAULT NULL, num_devis VARCHAR(255) NOT NULL, total_price DOUBLE PRECISION NOT NULL, produits JSON DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A9F48EAE19EB6921 ON quotations (client_id)');
        $this->addSql('COMMENT ON COLUMN quotations.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quotations.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quotations.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE roles (id UUID NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN roles.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quotations ADD CONSTRAINT FK_A9F48EAE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT fk_8b27c52b19eb6921');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04ad12469de2');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE devis (id UUID NOT NULL, client_id UUID NOT NULL, message TEXT DEFAULT NULL, num_devis VARCHAR(255) NOT NULL, total_price DOUBLE PRECISION NOT NULL, produits JSON DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8b27c52b19eb6921 ON devis (client_id)');
        $this->addSql('COMMENT ON COLUMN devis.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN devis.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN devis.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price INT NOT NULL, tva DOUBLE PRECISION NOT NULL, quantite INT DEFAULT NULL, prix_totale DOUBLE PRECISION DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_d34a04ad12469de2 ON product (category_id)');
        $this->addSql('COMMENT ON COLUMN product.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE facture (id UUID NOT NULL, num_facture VARCHAR(255) NOT NULL, num_devis VARCHAR(255) NOT NULL, paid BOOLEAN NOT NULL, date_facture TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, company UUID DEFAULT NULL, prix_total DOUBLE PRECISION NOT NULL, prix_paye DOUBLE PRECISION NOT NULL, reduction INT DEFAULT NULL, produits JSON NOT NULL, date_echeance TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, client JSON NOT NULL, messages VARCHAR(255) DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN facture.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN facture.company IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN facture.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT fk_8b27c52b19eb6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04ad12469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A12469DE2');
        $this->addSql('ALTER TABLE quotations DROP CONSTRAINT FK_A9F48EAE19EB6921');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE invoices');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE quotations');
        $this->addSql('DROP TABLE roles');
    }
}
