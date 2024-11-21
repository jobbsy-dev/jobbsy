<?php

namespace App\Doctrine\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Symfony\Component\Validator\Constraints\When;

#[When('dev')]
#[AsDoctrineListener(ToolEvents::postGenerateSchema)]
final class FixPostgreSQLDefaultSchemaListener
{
    /**
     * @throws Exception
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schemaManager = $args
            ->getEntityManager()
            ->getConnection()
            ->createSchemaManager();

        if (!$schemaManager instanceof PostgreSQLSchemaManager) {
            return;
        }

        $schema = $args->getSchema();

        foreach ($schemaManager->listSchemaNames() as $namespace) {
            if (!$schema->hasNamespace($namespace)) {
                $schema->createNamespace($namespace);
            }
        }
    }
}
