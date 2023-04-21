<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\LocationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class JobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnDetail(),
            TextField::new('title')
                ->setMaxLength(40),
            TextField::new('organization')
            ->hideOnIndex(),
            TextField::new('location')
                ->hideOnIndex(),
            TextField::new('salary')
                ->onlyOnForms(),
            ChoiceField::new('employmentType')
                ->onlyOnForms()
                ->setChoices(['Types' => EmploymentType::cases()])
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', EmploymentType::class)
                ->setFormTypeOption('choice_label', function (EmploymentType $enum): string {
                    return $enum->value;
                }),
            ChoiceField::new('locationType')
                ->onlyOnForms()
                ->setChoices(['Location types' => LocationType::cases()])
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', LocationType::class)
                ->setFormTypeOption('choice_label', function (LocationType $enum): string {
                    return $enum->value;
                }),
            ArrayField::new('tags'),
            UrlField::new('url'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/y', 'none')
                ->hideOnForm(),
            Field::new('organizationImage.file')
                ->onlyOnForms()
                ->setFormType(FileType::class)
                ->setLabel('Organization Image File'),
            TextField::new('source')
                ->onlyOnDetail(),
            DateTimeField::new('pinnedUntil')
                ->setFormat('d/M/Y', 'none')
                ->onlyOnForms(),
            TextField::new('tweetId')
                ->onlyOnForms(),
            TextField::new('contactEmail')
                ->onlyOnForms(),
            TextField::new('industry')
                ->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title', 'organization'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(30);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title')
            ->add('organization')
            ->add('employmentType')
            ->add('pinnedUntil')
            ->add('source')
            ->add('createdAt')
            ->add('industry')
        ;
    }
}
