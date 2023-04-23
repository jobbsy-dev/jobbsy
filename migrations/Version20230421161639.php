<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230421161639 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE entry (id UUID NOT NULL, feed_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B219D7036AC99F1 ON entry (link)');
        $this->addSql('CREATE INDEX IDX_2B219D7051A5BC03 ON entry (feed_id)');
        $this->addSql('COMMENT ON COLUMN entry.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN entry.feed_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN entry.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN entry.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE event (id UUID NOT NULL, name VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, location TEXT DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, abstract TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, url VARCHAR(255) NOT NULL, attendance_mode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN event.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN event.start_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.end_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE feed (id UUID NOT NULL, url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image_url VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN feed.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN feed.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN feed.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN feed.image_dimensions IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE job (id UUID NOT NULL, title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, employment_type VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, tags JSON NOT NULL, url VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, organization_image_url VARCHAR(255) DEFAULT NULL, click_count INT DEFAULT 0 NOT NULL, source VARCHAR(255) DEFAULT NULL, pinned_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tweet_id VARCHAR(255) DEFAULT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, salary VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, location_type VARCHAR(255) DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, organization_image_name VARCHAR(255) DEFAULT NULL, organization_image_path VARCHAR(255) DEFAULT NULL, organization_image_original_name VARCHAR(255) DEFAULT NULL, organization_image_mime_type VARCHAR(255) DEFAULT NULL, organization_image_size INT DEFAULT NULL, organization_image_dimensions TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN job.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN job.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN job.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN job.pinned_until IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN job.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN job.organization_image_dimensions IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE source (id UUID NOT NULL, url VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN source.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN source.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D7051A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entry DROP CONSTRAINT FK_2B219D7051A5BC03');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE feed');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
