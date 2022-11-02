<?php

namespace App\Controller\Admin;

use App\Entity\Feed;
use App\News\FeedType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class FeedCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Feed::class;
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
                    $choices = array_map(static fn (?FeedType $unit) => [$unit->value => $unit], FeedType::cases());

                    return array_merge(...$choices);
                })
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', FeedType::class)
                ->setFormTypeOption('choice_label', function (FeedType $enum) {
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
