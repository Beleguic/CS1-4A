<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303205627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // Mettre à jour la table user existante pour correspondre au nouveau schéma
        $this->addSql('ALTER TABLE "user" ALTER COLUMN id TYPE UUID USING (gen_random_uuid())');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS company_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS lastname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS firstname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS activation_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS enabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS reset_password_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS verified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP COLUMN IF EXISTS is_verified');
        
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE IF NOT EXISTS category (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS client (id UUID NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, numero_telephone VARCHAR(255) NOT NULL, address_number VARCHAR(255) DEFAULT NULL, address_type VARCHAR(255) DEFAULT NULL, address_name VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN client.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN client.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS company (id UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, invoice_email VARCHAR(255) DEFAULT NULL, address_number VARCHAR(255) DEFAULT NULL, address_type VARCHAR(255) DEFAULT NULL, address_name VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, company_number VARCHAR(20) DEFAULT NULL, iban VARCHAR(34) DEFAULT NULL, bic VARCHAR(11) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN company.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS contact (id UUID NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN contact.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS devis (id UUID NOT NULL, client_id UUID NOT NULL, message TEXT DEFAULT NULL, num_devis VARCHAR(255) NOT NULL, entreprise VARCHAR(255) NOT NULL, total_price DOUBLE PRECISION NOT NULL, produits JSON DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_8B27C52B19EB6921 ON devis (client_id)');
        $this->addSql('COMMENT ON COLUMN devis.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN devis.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN devis.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS facture (id UUID NOT NULL, num_facture VARCHAR(255) NOT NULL, num_devis VARCHAR(255) NOT NULL, paid BOOLEAN NOT NULL, date_facture TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, company UUID DEFAULT NULL, prix_total DOUBLE PRECISION NOT NULL, prix_paye DOUBLE PRECISION NOT NULL, reduction INT DEFAULT NULL, produits JSON NOT NULL, date_echeance TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, client JSON NOT NULL, messages VARCHAR(255) DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN facture.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN facture.company IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN facture.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS product (id INT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price INT NOT NULL, tva DOUBLE PRECISION NOT NULL, quantite INT DEFAULT NULL, prix_totale DOUBLE PRECISION DEFAULT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('COMMENT ON COLUMN product.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS request_new_company_user (id UUID NOT NULL, company_id UUID NOT NULL, role VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN request_new_company_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN request_new_company_user.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN request_new_company_user.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS role (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_8D93D649979B1AD6 ON "user" (company_id)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".verified_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        
        // Ajouter les contraintes de clé étrangère seulement si elles n'existent pas déjà
        $this->addSql('DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = \'FK_8B27C52B19EB6921\') THEN
                ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
            END IF;
        END $$;');
        $this->addSql('DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = \'FK_D34A04AD12469DE2\') THEN
                ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
            END IF;
        END $$;');
        $this->addSql('DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = \'FK_8D93D649979B1AD6\') THEN
                ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
            END IF;
        END $$;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B19EB6921');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649979B1AD6');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE request_new_company_user');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
