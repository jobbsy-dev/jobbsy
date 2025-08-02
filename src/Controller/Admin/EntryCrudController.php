<?php

namespace App\Controller\Admin;

use App\Entity\News\Entry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

/**
 * @extends AbstractCrudController<Entry>
 */
final class EntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entry::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnDetail(),
            TextField::new('title')
                ->setMaxLength(35),
            UrlField::new('link'),
            AssociationField::new('feed'),
            TextareaField::new('description')
                ->onlyOnForms(),
            DateTimeField::new('publishedAt'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title')
        ;
    }
}
