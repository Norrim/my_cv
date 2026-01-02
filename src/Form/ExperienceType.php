<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'experience.form.title',
            ])
            ->add('company', TextType::class, [
                'label' => 'experience.form.company',
            ])
            ->add('startDate', DateType::class, [
                'label' => 'experience.form.start_date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'experience.form.end_date',
                'widget' => 'single_text',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'experience.form.description',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'experience.form.position',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
            'translation_domain' => 'forms',
        ]);
    }
}
