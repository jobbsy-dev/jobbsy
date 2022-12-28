<?php

namespace App\Controller\Admin;

use App\Entity\CommunityEvent\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnDetail(),
            TextField::new('name')
                ->setMaxLength(35),
            UrlField::new('url'),
            DateField::new('startDate'),
            DateField::new('endDate'),
            TextField::new('location'),
            TextareaField::new('abstract')
                ->onlyOnForms(),
            CountryField::new('country')
                ->setFormTypeOption('preferred_choices', ['FR', 'DE', 'ES', 'UK', 'IT', 'PL']),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
        ;
    }
}
