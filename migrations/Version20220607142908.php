<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607142908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_register (id SERIAL NOT NULL, name VARCHAR(1024) DEFAULT NULL, serial VARCHAR(1024) NOT NULL, bind_to_user VARCHAR(1024) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D7AB1D9D374C9DC ON cash_register (serial)');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, barcode VARCHAR(1024) NOT NULL, name VARCHAR(1024) NOT NULL, price NUMERIC(10, 2) NOT NULL, vat_class SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD97AE0266 ON product (barcode)');
        $this->addSql('CREATE TABLE receipt (id SERIAL NOT NULL, cash_register_id INT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5399B645A917CC69 ON receipt (cash_register_id)');
        $this->addSql('COMMENT ON COLUMN receipt.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN receipt.finished_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE receipt_row (id SERIAL NOT NULL, receipt_id INT NOT NULL, position INT NOT NULL, product_id INT NOT NULL, product_name VARCHAR(1024) NOT NULL, price NUMERIC(10, 2) NOT NULL, vat_class SMALLINT NOT NULL, vat_percent NUMERIC(3, 2) DEFAULT \'0\' NOT NULL, vat_amount NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F6A3F4912B5CA896 ON receipt_row (receipt_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6A3F4912B5CA896462CE4F5 ON receipt_row (receipt_id, position)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6A3F4912B5CA8964584665A ON receipt_row (receipt_id, product_id)');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645A917CC69 FOREIGN KEY (cash_register_id) REFERENCES cash_register (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_row ADD CONSTRAINT FK_F6A3F4912B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE receipt DROP CONSTRAINT FK_5399B645A917CC69');
        $this->addSql('ALTER TABLE receipt_row DROP CONSTRAINT FK_F6A3F4912B5CA896');
        $this->addSql('DROP TABLE cash_register');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE receipt_row');
    }
}
