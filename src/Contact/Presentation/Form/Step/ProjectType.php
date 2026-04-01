<?php

declare(strict_types=1);

namespace App\Contact\Presentation\Form\Step;

use App\Contact\Domain\Dto\Step\ProjectDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<ProjectDto> */
final class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projectDescription', TextareaType::class, [
                'label' => 'contact.step3.project_description.label',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'contact.step3.project_description.placeholder',
                ],
            ])
            ->add('techStack', TextareaType::class, [
                'label' => 'contact.step3.tech_stack.label',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'contact.step3.tech_stack.placeholder',
                ],
            ])
            ->add('estimatedDuration', TextType::class, [
                'label' => 'contact.step3.estimated_duration.label',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'contact.step3.estimated_duration.placeholder',
                ],
            ])
            ->add('freeMessage', TextareaType::class, [
                'label' => 'contact.step3.free_message.label',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'contact.step3.free_message.placeholder',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectDto::class,
        ]);
    }
}
