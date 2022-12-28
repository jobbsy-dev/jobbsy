<?php

namespace App\Controller\Admin;

use App\CommunityEvent\SourceType;
use App\Entity\CommunityEvent\Source;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class SourceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Source::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnDetail(),
            TextField::new('name')
                ->setMaxLength(35),
            UrlField::new('url'),
            ChoiceField::new('type')
                ->onlyOnForms()
                ->setChoices(function () {
                    $choices = array_map(static fn (?SourceType $unit) => [$unit->value => $unit], SourceType::cases());

                    return array_merge(...$choices);
                })
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', SourceType::class)
                ->setFormTypeOption('choice_label', function (SourceType $enum) {
                    return $enum->value;
                }),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
        ;
    }
}
