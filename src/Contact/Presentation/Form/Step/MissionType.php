<?php

declare(strict_types=1);

namespace App\Contact\Presentation\Form\Step;

use App\Contact\Application\DTO\Step\MissionDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<MissionDto> */
final class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contractType', ChoiceType::class, [
                'label' => 'contact.step2.contract_type.label',
                'choices' => [
                    'contact.step2.contract_type.freelance' => 'freelance',
                    'contact.step2.contract_type.cdi' => 'cdi',
                    'contact.step2.contract_type.short_mission' => 'short_mission',
                ],
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'form-label fw-semibold'],
            ])
            ->add('workMode', ChoiceType::class, [
                'label' => 'contact.step2.work_mode.label',
                'choices' => [
                    'contact.step2.work_mode.remote' => 'remote',
                    'contact.step2.work_mode.hybrid' => 'hybrid',
                    'contact.step2.work_mode.onsite' => 'onsite',
                ],
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'form-label fw-semibold'],
            ])
            ->add('location', TextType::class, [
                'label' => 'contact.step2.location.label',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'contact.step2.location.placeholder',
                ],
            ])
            ->add('dailyRate', TextType::class, [
                'label' => 'contact.step2.daily_rate.label',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'contact.step2.daily_rate.placeholder',
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'contact.step2.start_date.label',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MissionDto::class,
        ]);
    }
}
