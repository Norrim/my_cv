<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Recommendation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecommendationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('currentRole', TextType::class, [
                'label' => 'Rôle actuel',
                'required' => false,
            ])
            ->add('roleAtThatTime', TextType::class, [
                'label' => 'Rôle à l\'époque',
                'required' => false,
            ])
            ->add('recommandedAt', DateType::class, [
                'label' => 'Recommandé le',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position',
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recommendation::class,
        ]);
    }
}
