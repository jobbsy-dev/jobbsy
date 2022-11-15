<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221115133013 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__entry AS SELECT id, feed_id, title, link, description, created_at, published_at FROM entry');
        $this->addSql('DROP TABLE entry');
        $this->addSql('CREATE TABLE entry (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , feed_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, description CLOB NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_2B219D7051A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO entry (id, feed_id, title, link, description, created_at, published_at) SELECT id, feed_id, title, link, description, created_at, published_at FROM __temp__entry');
        $this->addSql('DROP TABLE __temp__entry');
        $this->addSql('CREATE INDEX IDX_2B219D7051A5BC03 ON entry (feed_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B219D7036AC99F1 ON entry (link)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__entry AS SELECT id, feed_id, title, link, description, created_at, published_at FROM entry');
        $this->addSql('DROP TABLE entry');
        $this->addSql('CREATE TABLE entry (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , feed_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, description CLOB NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , published_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_2B219D7051A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO entry (id, feed_id, title, link, description, created_at, published_at) SELECT id, feed_id, title, link, description, created_at, published_at FROM __temp__entry');
        $this->addSql('DROP TABLE __temp__entry');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B219D7036AC99F1 ON entry (link)');
        $this->addSql('CREATE INDEX IDX_2B219D7051A5BC03 ON entry (feed_id)');
    }
}
