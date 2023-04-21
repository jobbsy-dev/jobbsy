<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230421152226 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE post');
        $this->addSql('ALTER TABLE feed ADD COLUMN image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD COLUMN image_original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD COLUMN image_mime_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD COLUMN image_dimensions CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD COLUMN organization_image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD COLUMN organization_image_original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD COLUMN organization_image_mime_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD COLUMN organization_image_dimensions CLOB DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE post (id CHAR(36) NOT NULL COLLATE "BINARY" --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL COLLATE "BINARY", slug VARCHAR(255) NOT NULL COLLATE "BINARY", summary VARCHAR(255) NOT NULL COLLATE "BINARY", content CLOB NOT NULL COLLATE "BINARY", published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 ON post (slug)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__feed AS SELECT id, url, name, created_at, type, updated_at, image_url, image_name, image_size FROM feed');
        $this->addSql('DROP TABLE feed');
        $this->addSql('CREATE TABLE feed (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , type VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , image_url VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO feed (id, url, name, created_at, type, updated_at, image_url, image_name, image_size) SELECT id, url, name, created_at, type, updated_at, image_url, image_name, image_size FROM __temp__feed');
        $this->addSql('DROP TABLE __temp__feed');
        $this->addSql('CREATE TEMPORARY TABLE __temp__job AS SELECT id, title, location, created_at, employment_type, organization, tags, url, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type, industry, description, organization_image_name, organization_image_size FROM job');
        $this->addSql('DROP TABLE job');
        $this->addSql('CREATE TABLE job (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , employment_type VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, tags CLOB NOT NULL --(DC2Type:json)
        , url VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , organization_image_url VARCHAR(255) DEFAULT NULL, click_count INTEGER DEFAULT 0 NOT NULL, source VARCHAR(255) DEFAULT NULL, pinned_until DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , tweet_id VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , salary VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, location_type VARCHAR(255) DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, organization_image_name VARCHAR(255) DEFAULT NULL, organization_image_size INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO job (id, title, location, created_at, employment_type, organization, tags, url, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type, industry, description, organization_image_name, organization_image_size) SELECT id, title, location, created_at, employment_type, organization, tags, url, updated_at, organization_image_url, click_count, source, pinned_until, tweet_id, published_at, salary, contact_email, location_type, industry, description, organization_image_name, organization_image_size FROM __temp__job');
        $this->addSql('DROP TABLE __temp__job');
    }
}
