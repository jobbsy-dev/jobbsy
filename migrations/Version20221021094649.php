<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221021094649 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job RENAME COLUMN email TO contact_email');
        $this->addSql('ALTER TABLE job ADD COLUMN location_type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job RENAME COLUMN contact_email TO email');
        $this->addSql('ALTER TABLE job DROP COLUMN location_type');
    }
}
