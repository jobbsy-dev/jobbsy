<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221228143309 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE source (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, name, start_date, end_date, location, abstract, created_at, url, country FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , location CLOB DEFAULT NULL, abstract CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , url VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, attendance_mode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO event (id, name, start_date, end_date, location, abstract, created_at, url, country) SELECT id, name, start_date, end_date, location, abstract, created_at, url, country FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE source');
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, name, start_date, end_date, location, country, abstract, created_at, url FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , location CLOB NOT NULL, country VARCHAR(255) NOT NULL, abstract CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO event (id, name, start_date, end_date, location, country, abstract, created_at, url) SELECT id, name, start_date, end_date, location, country, abstract, created_at, url FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
