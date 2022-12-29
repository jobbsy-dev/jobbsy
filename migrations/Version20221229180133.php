<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221229180133 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__source AS SELECT id, url FROM source');
        $this->addSql('DROP TABLE source');
        $this->addSql('CREATE TABLE source (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO source (id, url, created_at) SELECT id, url, DATETIME("now") FROM __temp__source');
        $this->addSql('DROP TABLE __temp__source');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__source AS SELECT id, url FROM source');
        $this->addSql('DROP TABLE source');
        $this->addSql('CREATE TABLE source (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO source (id, url) SELECT id, url FROM __temp__source');
        $this->addSql('DROP TABLE __temp__source');
    }
}
