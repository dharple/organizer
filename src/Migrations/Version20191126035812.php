<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126035812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE box_type');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, label, description, location_id FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB NOT NULL COLLATE BINARY, location_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO box (id, label, description, location_id) SELECT id, label, description, location_id FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE box_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, label, description, location_id FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description CLOB NOT NULL, location_id INTEGER NOT NULL, box_type_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO box (id, label, description, location_id) SELECT id, label, description, location_id FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
    }
}
