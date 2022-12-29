<?php

namespace App\Controller\Admin;

use App\CommunityEvent\AttendanceMode;
use App\Entity\CommunityEvent\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

final class EventCrudController extends AbstractCrudController
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
            ChoiceField::new('attendanceMode')
                ->onlyOnForms()
                ->setChoices(function (): array {
                    $choices = array_map(static fn (?AttendanceMode $unit): array => [$unit->value => $unit], AttendanceMode::cases());

                    return array_merge(...$choices);
                })
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', AttendanceMode::class)
                ->setFormTypeOption('choice_label', function (AttendanceMode $enum): string {
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
