<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508140055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fuel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE price (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, station_id INTEGER NOT NULL, fuel_id INTEGER NOT NULL, amount NUMERIC(4, 3) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_CAC822D921BDB235 ON price (station_id)');
        $this->addSql('CREATE INDEX IDX_CAC822D997C79677 ON price (fuel_id)');
        $this->addSql('CREATE TABLE station (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NULL, address VARCHAR(255) NOT NULL, postcode INTEGER NOT NULL, city VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fuel');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE station');
    }
}
