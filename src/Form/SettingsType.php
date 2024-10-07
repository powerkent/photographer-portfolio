<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name', null, [
                'label' => 'Ton nom',
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Moi',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)',
                    ])
                ],
            ]);

        if ($options['data']->getImagePath()) {
            $builder->add('deleteImage', CheckboxType::class, [
                'label' => 'Supprimer l\'image actuelle',
                'required' => false,
                'mapped' => false,
            ]);
        }

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email de contact',
            ])
            ->add('phone', TelType::class, [
                'label' => 'Ton numéro de téléphone'
            ])
            ->add('address', TextAreaType::class, [
                'label' => 'Adresse',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre sur la HP',
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'Sous-titre sur la HP',
            ])
            ->add('subsubtitle', TextType::class, [
                'label' => 'Sous-sous-titre sur la HP',
            ])
            ->add('description1', TextareaType::class, [
                'label' => 'description gauche sur la page about me',
            ])
            ->add('description2', TextareaType::class, [
                'label' => 'description droite sur la page about me',
            ])
            ->add('googleApiKey', TextType::class, [
                'label' => 'google api key pour la map',
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'scale' => 5,
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'scale' => 5,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
