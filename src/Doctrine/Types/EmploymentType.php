<?php

namespace App\Doctrine\Types;

use App\EmploymentType as EmploymentTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EmploymentType extends Type
{
    /**
     * @param EmploymentTypeEnum $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return EmploymentTypeEnum::from($value);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return 'employment_type';
    }
}
