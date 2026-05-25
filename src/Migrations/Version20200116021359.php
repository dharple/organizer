<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200116021359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, label, description, created_at, updated_at, deleted_at, location_id, box_model_id FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, box_number INTEGER NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL --(DC2Type:datetime)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime)
        , location_id INTEGER DEFAULT NULL, box_model_id INTEGER DEFAULT NULL, deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime)
        )');
        $this->addSql('INSERT INTO box (id, box_number, label, description, created_at, updated_at, deleted_at, location_id, box_model_id) SELECT id, id, label, description, created_at, updated_at, deleted_at, location_id, box_model_id FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
        $this->addSql('CREATE UNIQUE INDEX box_number_uniq ON box (box_number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX box_number_uniq');
        $this->addSql('CREATE TEMPORARY TABLE __temp__box AS SELECT id, description, label, created_at, updated_at, deleted_at, box_model_id, location_id FROM box');
        $this->addSql('DROP TABLE box');
        $this->addSql('CREATE TABLE box (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, description CLOB DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime)
        , box_model_id INTEGER DEFAULT NULL, location_id INTEGER DEFAULT NULL, deleted_at DATETIME DEFAULT \'NULL --(DC2Type:datetime)\' --(DC2Type:datetime)
        )');
        $this->addSql('INSERT INTO box (id, description, label, created_at, updated_at, deleted_at, box_model_id, location_id) SELECT id, description, label, created_at, updated_at, deleted_at, box_model_id, location_id FROM __temp__box');
        $this->addSql('DROP TABLE __temp__box');
    }
}
