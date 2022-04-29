<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20220429065805 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job ADD pinned_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN job.pinned_until IS \'(DC2Type:datetime_immutable)\'');
    }

    public function postUp(Schema $schema): void
    {
        $this->connection->update('job', [
            'pinned_until' => new \DateTimeImmutable('+1 month')
        ], ['pinned' => true], [Types::DATETIME_IMMUTABLE]);
        $this->connection->executeQuery('ALTER TABLE job DROP pinned');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE job ADD pinned BOOLEAN DEFAULT false NOT NULL');
    }

    public function postDown(Schema $schema): void
    {
        $this->connection->executeQuery(
            'UPDATE job SET pinned = :pinned WHERE pinned_until >= :now',
            ['pinned' => true, 'now' => (new \DateTimeImmutable())->format('c')],
            [Types::BOOLEAN, Types::DATETIME_IMMUTABLE]
        );
        $this->connection->executeQuery('ALTER TABLE job DROP pinned_until');
    }
}
