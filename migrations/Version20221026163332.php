<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221026163332 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , location CLOB NOT NULL, abstract CLOB DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TEMPORARY TABLE __temp__job AS SELECT id, title, location, created_at, employment_type, organization, tags, url, organization_image_name, organization_image_size, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type FROM job');
        $this->addSql('DROP TABLE job');
        $this->addSql('CREATE TABLE job (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , employment_type VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, tags CLOB NOT NULL --(DC2Type:json)
        , url VARCHAR(255) NOT NULL, organization_image_name VARCHAR(255) DEFAULT NULL, organization_image_size INTEGER DEFAULT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , organization_image_url VARCHAR(255) DEFAULT NULL, click_count INTEGER DEFAULT 0 NOT NULL, source VARCHAR(255) DEFAULT NULL, pinned_until DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , tweet_id VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , salary VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, location_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO job (id, title, location, created_at, employment_type, organization, tags, url, organization_image_name, organization_image_size, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type) SELECT id, title, location, created_at, employment_type, organization, tags, url, organization_image_name, organization_image_size, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type FROM __temp__job');
        $this->addSql('DROP TABLE __temp__job');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TEMPORARY TABLE __temp__job AS SELECT id, title, location, created_at, employment_type, organization, tags, url, organization_image_name, organization_image_size, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type FROM job');
        $this->addSql('DROP TABLE job');
        $this->addSql('CREATE TABLE job (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , employment_type VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, tags CLOB NOT NULL --(DC2Type:json)
        , url VARCHAR(255) NOT NULL, organization_image_name VARCHAR(255) DEFAULT NULL, organization_image_size INTEGER DEFAULT NULL, updated_at DATETIME NOT NULL, organization_image_url VARCHAR(255) DEFAULT NULL, click_count INTEGER DEFAULT 0 NOT NULL, source VARCHAR(255) DEFAULT NULL, pinned_until DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , tweet_id VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, salary VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, location_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO job (id, title, location, created_at, employment_type, organization, tags, url, organization_image_name, organization_image_size, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type) SELECT id, title, location, created_at, employment_type, organization, tags, url, organization_image_name, organization_image_size, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type FROM __temp__job');
        $this->addSql('DROP TABLE __temp__job');
    }
}
