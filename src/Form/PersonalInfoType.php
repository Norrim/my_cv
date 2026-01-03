<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\PersonalInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PersonalInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'personal_info.form.firstname',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'personal_info.form.name',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'personal_info.form.title',
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'personal_info.form.phone_number',
                'required' => false,
            ])
            ->add('localisation', TextType::class, [
                'label' => 'personal_info.form.localisation',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'personal_info.form.email',
                'required' => false,
            ])
            ->add('about', TextareaType::class, [
                'label' => 'personal_info.form.about',
                'required' => false,
                'attr' => [
                    'rows' => 10,
                    'class' => 'tinymce',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalInfo::class,
            'translation_domain' => 'messages',
        ]);
    }
}
