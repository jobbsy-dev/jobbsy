<?php

namespace App\Controller\Admin;

use App\Entity\News\Feed;
use App\News\Aggregator\FeedType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class FeedCrudController extends AbstractCrudController
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
                ->setChoices(['Types' => FeedType::cases()])
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', FeedType::class)
                ->setFormTypeOption('choice_label', static function (FeedType $enum): string {
                    return $enum->value;
                }),
            UrlField::new('imageUrl')
                ->onlyOnForms(),
            Field::new('imageFile')
                ->onlyOnForms()
                ->setFormType(FileType::class),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
        ;
    }
}
