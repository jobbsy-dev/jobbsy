<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027073220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, name, start_date, end_date, location, abstract, created_at, url FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , location CLOB NOT NULL, abstract CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , url VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO event (id, name, start_date, end_date, location, abstract, created_at, url, country) SELECT id, name, start_date, end_date, location, abstract, created_at, url, "fr" FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, name, start_date, end_date, location, abstract, created_at, url FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , location CLOB NOT NULL, abstract CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , url VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INTEGER DEFAULT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO event (id, name, start_date, end_date, location, abstract, created_at, url) SELECT id, name, start_date, end_date, location, abstract, created_at, url FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
