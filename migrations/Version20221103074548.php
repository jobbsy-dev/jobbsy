<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221103074548 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE feed ADD COLUMN image_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD COLUMN image_size INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD COLUMN updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE feed ADD COLUMN image_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__feed AS SELECT id, url, name, created_at, type FROM feed');
        $this->addSql('DROP TABLE feed');
        $this->addSql('CREATE TABLE feed (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO feed (id, url, name, created_at, type) SELECT id, url, name, created_at, type FROM __temp__feed');
        $this->addSql('DROP TABLE __temp__feed');
    }
}
