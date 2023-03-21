<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230321065412 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE job SET employment_type='fulltime' WHERE employment_type='full_time'");
        $this->addSql("UPDATE job SET location_type='onsite' WHERE location_type='one_site'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE job SET employment_type='full_time' WHERE employment_type='fulltime'");
        $this->addSql("UPDATE job SET location_type='one_site' WHERE location_type='onsite'");
    }
}
