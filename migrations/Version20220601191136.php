<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220601191136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fuel ADD COLUMN type VARCHAR(5) NOT NULL');
        $this->addSql('INSERT INTO fuel VALUES (1, "Gazole", "GAZOLE", "B7")');
        $this->addSql('INSERT INTO fuel VALUES (2, "SP95", "SP95", "E5")');
        $this->addSql('INSERT INTO fuel VALUES (3, "SP98", "SP98", "E5")');
        $this->addSql('INSERT INTO fuel VALUES (4, "E10", "SP95-E10", "E10")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fuel DROP COLUMN type');
        $this->addSql('DELETE FROM fuel');
    }
}
