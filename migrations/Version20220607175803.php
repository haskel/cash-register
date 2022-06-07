<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607175803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ALTER price TYPE NUMERIC(12, 2)');
        $this->addSql('ALTER TABLE receipt_row ALTER price TYPE NUMERIC(12, 2)');
        $this->addSql('ALTER TABLE receipt_row ALTER vat_percent TYPE NUMERIC(5, 2)');
        $this->addSql('ALTER TABLE receipt_row ALTER vat_amount TYPE NUMERIC(12, 2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product ALTER price TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE receipt_row ALTER price TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE receipt_row ALTER vat_percent TYPE NUMERIC(3, 2)');
        $this->addSql('ALTER TABLE receipt_row ALTER vat_amount TYPE NUMERIC(10, 2)');
    }
}
