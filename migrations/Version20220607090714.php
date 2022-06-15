<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220607090714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', employment_type VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, tags LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', url VARCHAR(255) NOT NULL, organization_image_name VARCHAR(255) DEFAULT NULL, organization_image_size INT DEFAULT NULL, updated_at DATETIME NOT NULL, organization_image_url VARCHAR(255) DEFAULT NULL, click_count INT DEFAULT 0 NOT NULL, source VARCHAR(255) DEFAULT NULL, pinned_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
