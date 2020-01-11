<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129174858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__box_model AS SELECT id, label, make, model, size, color, latch FROM box_model');
        $this->addSql('DROP TABLE box_model');
        $this->addSql('CREATE TABLE box_model (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, make VARCHAR(64) DEFAULT NULL, model VARCHAR(64) DEFAULT NULL, size VARCHAR(64) DEFAULT NULL, color VARCHAR(64) DEFAULT NULL, latch VARCHAR(16) DEFAULT NULL)');
        $this->addSql('INSERT INTO box_model (id, label, make, model, size, color, latch) SELECT id, label, make, model, size, color, latch FROM __temp__box_model');
        $this->addSql('DROP TABLE __temp__box_model');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__box_model AS SELECT id, label, make, model, size, color, latch FROM box_model');
        $this->addSql('DROP TABLE box_model');
        $this->addSql('CREATE TABLE box_model (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, make VARCHAR(255) DEFAULT NULL COLLATE BINARY, model VARCHAR(255) DEFAULT NULL COLLATE BINARY, size VARCHAR(255) DEFAULT NULL COLLATE BINARY, color VARCHAR(255) DEFAULT NULL COLLATE BINARY, latch BOOLEAN DEFAULT \'false\' NOT NULL)');
        $this->addSql('INSERT INTO box_model (id, label, make, model, size, color, latch) SELECT id, label, make, model, size, color, latch FROM __temp__box_model');
        $this->addSql('DROP TABLE __temp__box_model');
    }
}
