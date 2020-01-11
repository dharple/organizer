<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200102030525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, label, description, location_id, box_model_id FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, location_id INTEGER DEFAULT NULL, box_model_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO box (id, label, description, location_id, box_model_id, created_at, updated_at) SELECT id, label, description, location_id, box_model_id, datetime(), datetime() FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box_model AS SELECT id, label, make, model, size, color, latch FROM box_model');
        $this->addSql('DROP TABLE box_model');
        $this->addSql('CREATE TABLE box_model (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, make VARCHAR(64) DEFAULT NULL COLLATE BINARY, model VARCHAR(64) DEFAULT NULL COLLATE BINARY, size VARCHAR(64) DEFAULT NULL COLLATE BINARY, color VARCHAR(64) DEFAULT NULL COLLATE BINARY, latch VARCHAR(16) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO box_model (id, label, make, model, size, color, latch, created_at, updated_at) SELECT id, label, make, model, size, color, latch, datetime(), datetime() FROM __temp__box_model');
        $this->addSql('DROP TABLE __temp__box_model');
        $this->addSql('CREATE TEMPORARY TABLE __temp__location AS SELECT id, label, parent_location_id FROM location');
        $this->addSql('DROP TABLE location');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, parent_location_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO location (id, label, parent_location_id, created_at, updated_at) SELECT id, label, parent_location_id, datetime(), datetime() FROM __temp__location');
        $this->addSql('DROP TABLE __temp__location');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME NULL, email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO user (id, email, roles, password, created_at, updated_at) SELECT id, email, roles, password, datetime(), datetime() FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, label, description, location_id, box_model_id FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, location_id INTEGER DEFAULT NULL, box_model_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO box (id, label, description, location_id, box_model_id) SELECT id, label, description, location_id, box_model_id FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box_model AS SELECT id, label, make, model, size, color, latch FROM box_model');
        $this->addSql('DROP TABLE box_model');
        $this->addSql('CREATE TABLE box_model (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, make VARCHAR(64) DEFAULT NULL, model VARCHAR(64) DEFAULT NULL, size VARCHAR(64) DEFAULT NULL, color VARCHAR(64) DEFAULT NULL, latch VARCHAR(16) DEFAULT NULL)');
        $this->addSql('INSERT INTO box_model (id, label, make, model, size, color, latch) SELECT id, label, make, model, size, color, latch FROM __temp__box_model');
        $this->addSql('DROP TABLE __temp__box_model');
        $this->addSql('CREATE TEMPORARY TABLE __temp__location AS SELECT id, label, parent_location_id FROM location');
        $this->addSql('DROP TABLE location');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL, parent_location_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO location (id, label, parent_location_id) SELECT id, label, parent_location_id FROM __temp__location');
        $this->addSql('DROP TABLE __temp__location');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, email, roles, password) SELECT id, email, roles, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
