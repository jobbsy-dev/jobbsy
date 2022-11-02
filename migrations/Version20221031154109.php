<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221031154109 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, authors CLOB NOT NULL --(DC2Type:json)
        , description CLOB NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , source VARCHAR(255) NOT NULL, published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E6636AC99F1 ON article (link)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, title, slug, summary, content, published_at, created_at FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content CLOB NOT NULL, published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO post (id, title, slug, summary, content, published_at, created_at) SELECT id, title, slug, summary, content, published_at, created_at FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 ON post (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, title, slug, summary, content, published_at, created_at FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content CLOB NOT NULL, published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO post (id, title, slug, summary, content, published_at, created_at) SELECT id, title, slug, summary, content, published_at, created_at FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
    }
}
