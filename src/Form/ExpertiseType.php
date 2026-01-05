<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Expertise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpertiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'expertise.form.title.label',
                'attr' => ['class' => 'input form-control'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'expertise.form.content.label',
                'attr' => ['class' => 'input form-control', 'rows' => 3],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'expertise.form.position.label',
                'attr' => ['class' => 'input form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expertise::class,
        ]);
    }
}
