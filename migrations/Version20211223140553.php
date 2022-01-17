<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223140553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job ADD organization_image_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE job ADD organization_image_size INT NOT NULL');
        $this->addSql('ALTER TABLE job ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE job ALTER employment_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE job ALTER employment_type DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE job DROP organization_image_name');
        $this->addSql('ALTER TABLE job DROP organization_image_size');
        $this->addSql('ALTER TABLE job DROP updated_at');
        $this->addSql('ALTER TABLE job ALTER employment_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE job ALTER employment_type DROP DEFAULT');
    }
}
