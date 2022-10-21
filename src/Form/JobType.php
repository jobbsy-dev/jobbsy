<?php

namespace App\Form;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'form.label.job_title',
                'help' => 'form.help.job_title',
            ])
            ->add('location', TextType::class, [
                'label' => false,
                'help' => 'form.help.job_location',
            ])
            ->add('employmentType', EnumType::class, [
                'label' => 'form.label.employment_type',
                'class' => EmploymentType::class,
                'choice_label' => function (EmploymentType $employmentType) {
                    return sprintf('employment_type.%s', $employmentType->value);
                },
            ])
            ->add('organization', TextType::class, [
                'label' => 'form.label.organization',
            ])
            ->add('url', UrlType::class, [
                'label' => 'form.label.job_url',
                'help' => 'form.help.job_url',
            ])
            ->add('tags', TextType::class, [
                'label' => 'form.label.tags',
                'required' => false,
                'help' => 'form.help.tags',
            ])
            ->add('organizationImageFile', VichFileType::class, [
                'label' => 'form.label.organization_logo',
                'required' => false,
                'help' => 'form.help.organization_logo',
            ])
            ->add('donationAmount', MoneyType::class, [
                'label' => 'form.label.donation_amount',
                'divisor' => 100,
                'mapped' => false,
                'data' => 5000,
                'constraints' => [
                    new GreaterThanOrEqual(0),
                    new NotBlank(),
                ],
                'html5' => true,
                'attr' => [
                    'step' => 5,
                    'min' => 0,
                ],
            ])
            ->add('salary', TextType::class, [
                'label' => 'form.label.salary',
                'help' => 'form.help.salary',
                'required' => false,
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'form.label.email_address',
            ])
            ->add('locationType', EnumType::class, [
                'label' => false,
                'class' => LocationType::class,
                'choice_label' => function (LocationType $locationType) {
                    return sprintf('location_type.%s', $locationType->value);
                },
            ])
        ;

        $builder->get('tags')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    // transform the array to a string
                    return implode(', ', array_map('trim', $tagsAsArray));
                },
                function ($tagsAsString) {
                    if (null === $tagsAsString) {
                        return [];
                    }

                    // transform the string back to an array
                    return array_filter(array_map('trim', explode(',', $tagsAsString)));
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
