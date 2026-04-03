<?php

declare(strict_types=1);

namespace App\Contact\Presentation\Form\Step;

use App\Contact\Application\DTO\Step\IdentityDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<IdentityDto> */
final class IdentityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('lastName', TextType::class, [
            'label' => 'contact.step1.last_name.label',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'contact.step1.last_name.placeholder',
                'autocomplete' => 'family-name',
            ],
        ])->add('firstName', TextType::class, [
            'label' => 'contact.step1.first_name.label',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'contact.step1.first_name.placeholder',
                'autocomplete' => 'given-name',
            ],
        ])->add('email', EmailType::class, [
            'label' => 'contact.step1.email.label',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'contact.step1.email.placeholder',
                'autocomplete' => 'email',
            ],
        ])->add('company', TextType::class, [
            'label' => 'contact.step1.company.label',
            'required' => false,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'contact.step1.company.placeholder',
                'autocomplete' => 'organization',
            ],
        ])->add('phone', TelType::class, [
            'label' => 'contact.step1.phone.label',
            'required' => false,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'contact.step1.phone.placeholder',
                'autocomplete' => 'tel',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IdentityDto::class,
        ]);
    }
}
