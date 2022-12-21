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
use Vich\UploaderBundle\Form\Type\VichImageType;

class JobCrudController extends AbstractCrudController
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
                ->setMaxLength(35),
            TextField::new('organization'),
            TextField::new('location'),
            TextField::new('salary')
                ->onlyOnForms(),
            ChoiceField::new('employmentType')
                ->onlyOnForms()
                ->setChoices(function () {
                    $choices = array_map(static fn (?EmploymentType $unit) => [$unit->value => $unit], EmploymentType::cases());

                    return array_merge(...$choices);
                })
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', EmploymentType::class)
                ->setFormTypeOption('choice_label', function (EmploymentType $enum) {
                    return $enum->value;
                }),
            ChoiceField::new('locationType')
                ->onlyOnForms()
                ->setChoices(function () {
                    $choices = array_map(static fn (?LocationType $unit) => [$unit->value => $unit], LocationType::cases());

                    return array_merge(...$choices);
                })
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', LocationType::class)
                ->setFormTypeOption('choice_label', function (LocationType $enum) {
                    return $enum->value;
                }),
            ArrayField::new('tags'),
            UrlField::new('url'),
            DateTimeField::new('createdAt')
                ->setFormat('long', 'none')
                ->hideOnForm(),
            Field::new('organizationImageFile')
                ->onlyOnForms()
                ->setFormType(VichImageType::class),
            TextField::new('source')
                ->onlyOnIndex(),
            DateTimeField::new('pinnedUntil')
                ->setFormat('d/M/Y', 'none'),
            TextField::new('tweetId')
                ->onlyOnForms(),
            TextField::new('contactEmail')
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
