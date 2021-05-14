<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210512190815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, secondary_customer_id INT DEFAULT NULL, move NUMERIC(10, 5) NOT NULL, coin VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_ACF41FFE9395C3F3 (customer_id), INDEX IDX_ACF41FFE6BDE2B77 (secondary_customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BB861B1F64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_81398E09DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, postal_code INT NOT NULL, province VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFE6BDE2B77 FOREIGN KEY (secondary_customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1F64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09DCD6CC49');
        $this->addSql('ALTER TABLE balance DROP FOREIGN KEY FK_ACF41FFE9395C3F3');
        $this->addSql('ALTER TABLE balance DROP FOREIGN KEY FK_ACF41FFE6BDE2B77');
        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1F64D218E');
        $this->addSql('DROP TABLE balance');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE location');
    }
}
