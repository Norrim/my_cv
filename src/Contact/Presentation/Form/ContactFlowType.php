<?php

declare(strict_types=1);

namespace App\Contact\Presentation\Form;

use App\Contact\Application\DTO\ContactDataDto;
use App\Contact\Presentation\Form\Step\IdentityType;
use App\Contact\Presentation\Form\Step\MissionType;
use App\Contact\Presentation\Form\Step\ProjectType;
use Symfony\Component\Form\Flow\AbstractFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContactFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder->addStep('identity', IdentityType::class);
        $builder->addStep('mission', MissionType::class);
        $builder->addStep('project', ProjectType::class);
        $builder->add('navigator', ContactNavigatorType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDataDto::class,
            'step_property_path' => 'currentStep',
            'auto_reset' => false,
        ]);
    }
}
