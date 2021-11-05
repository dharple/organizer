<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211012094554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX box_number_uniq');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, box_number, label, description, created_at, updated_at, location_id, box_model_id, deleted_at FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, location_id INTEGER DEFAULT NULL, box_model_id INTEGER DEFAULT NULL, box_number INTEGER NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL --(DC2Type:datetime)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime)
        , deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime)
        , CONSTRAINT FK_8A9483A28B27543 FOREIGN KEY (box_model_id) REFERENCES box_model (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8A9483A64D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO box (id, box_number, label, description, created_at, updated_at, location_id, box_model_id, deleted_at) SELECT id, box_number, label, description, created_at, updated_at, location_id, box_model_id, deleted_at FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
        $this->addSql('CREATE UNIQUE INDEX box_number_uniq ON box (box_number)');
        $this->addSql('CREATE INDEX IDX_8A9483A28B27543 ON box (box_model_id)');
        $this->addSql('CREATE INDEX IDX_8A9483A64D218E ON box (location_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__location AS SELECT id, created_at, updated_at, deleted_at, label, parent_location_id FROM location');
        $this->addSql('DROP TABLE location');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_location_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime)
        , deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime)
        , label VARCHAR(255) NOT NULL COLLATE BINARY, hidden BOOLEAN NOT NULL, CONSTRAINT FK_5E9E89CB6D6133FE FOREIGN KEY (parent_location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO location (id, created_at, updated_at, deleted_at, label, parent_location_id, hidden) SELECT id, created_at, updated_at, deleted_at, label, parent_location_id, FALSE FROM __temp__location');
        $this->addSql('DROP TABLE __temp__location');
        $this->addSql('CREATE INDEX IDX_5E9E89CB6D6133FE ON location (parent_location_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_8A9483A28B27543');
        $this->addSql('DROP INDEX IDX_8A9483A64D218E');
        $this->addSql('DROP INDEX box_number_uniq');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, box_model_id, location_id, box_number, description, label, created_at, updated_at, deleted_at FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, box_model_id INTEGER DEFAULT NULL, location_id INTEGER DEFAULT NULL, box_number INTEGER NOT NULL, description CLOB DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime)
        , deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime)
        )');
        $this->addSql('INSERT INTO box (id, box_model_id, location_id, box_number, description, label, created_at, updated_at, deleted_at) SELECT id, box_model_id, location_id, box_number, description, label, created_at, updated_at, deleted_at FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
        $this->addSql('CREATE UNIQUE INDEX box_number_uniq ON box (box_number)');
        $this->addSql('DROP INDEX IDX_5E9E89CB6D6133FE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__location AS SELECT id, parent_location_id, label, created_at, updated_at, deleted_at FROM location');
        $this->addSql('DROP TABLE location');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_location_id INTEGER DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime)
        , deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime)
        )');
        $this->addSql('INSERT INTO location (id, parent_location_id, label, created_at, updated_at, deleted_at) SELECT id, parent_location_id, label, created_at, updated_at, deleted_at FROM __temp__location');
        $this->addSql('DROP TABLE __temp__location');
    }
}
