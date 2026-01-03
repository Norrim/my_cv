<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Social;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SocialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'social.form.name',
            ])
            ->add('url', UrlType::class, [
                'label' => 'social.form.url',
            ])
            ->add('iconClass', TextType::class, [
                'label' => 'social.form.icon_class',
                'help' => 'ex: feathericon-linkedin (feathericons.com)',
            ])
            ->add('position', IntegerType::class, [
                'label' => 'social.form.position',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Social::class,
            'translation_domain' => 'messages',
        ]);
    }
}
