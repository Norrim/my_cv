<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\ContactRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'input form-control',
                    'placeholder' => 'Full name',
                    'autocomplete' => 'on',
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input form-control',
                    'placeholder' => 'Email address',
                    'autocomplete' => 'on',
                ],
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'textarea form-control',
                    'placeholder' => 'Your Message',
                    'rows' => '4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequest::class,
        ]);
    }
    public function getBlockPrefix(): string
    {
        return 'contact';
    }
}
