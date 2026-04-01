<?php

declare(strict_types=1);

namespace App\Contact\Presentation\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Flow\FormFlowCursor;
use Symfony\Component\Form\Flow\Type\FinishFlowType;
use Symfony\Component\Form\Flow\Type\NextFlowType;
use Symfony\Component\Form\Flow\Type\PreviousFlowType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<mixed> */
final class ContactNavigatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('previous', PreviousFlowType::class, [
                'label' => 'contact.wizard.previous',
                'include_if' => fn (FormFlowCursor $cursor): bool => !$cursor->isFirstStep(),
            ])
            ->add('next', NextFlowType::class, [
                'label' => 'contact.wizard.next',
                'include_if' => fn (FormFlowCursor $cursor): bool => !$cursor->isLastStep(),
            ])
            ->add('finish', FinishFlowType::class, [
                'label' => 'contact.wizard.send',
                'include_if' => fn (FormFlowCursor $cursor): bool => $cursor->isLastStep(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
        ]);
    }
}
