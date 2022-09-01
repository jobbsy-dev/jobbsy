<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('donationAmount', MoneyType::class, [
                'label' => 'form.label.donation_amount',
                'divisor' => 100,
                'mapped' => false,
                'data' => 5000,
                'constraints' => [
                    new GreaterThan(0),
                    new NotBlank(),
                ],
                'html5' => true,
                'attr' => [
                    'step' => 5,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
