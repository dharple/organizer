<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129172512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE box_model ADD COLUMN make VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE box_model ADD COLUMN model VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE box_model ADD COLUMN size VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE box_model ADD COLUMN color VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE box_model ADD COLUMN latch BOOLEAN NOT NULL DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__box_model AS SELECT id, label FROM box_model');
        $this->addSql('DROP TABLE box_model');
        $this->addSql('CREATE TABLE box_model (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO box_model (id, label) SELECT id, label FROM __temp__box_model');
        $this->addSql('DROP TABLE __temp__box_model');
    }
}
