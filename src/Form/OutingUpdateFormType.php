<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Outing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class OutingUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une nom de sortie',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères',
                        'max' => 40,
                        'maxMessage' => 'Le nom doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('outingDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la sortie',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une date de sortie',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(),
                        'message' => 'La date de la sortie doit être ultérieure à la date actuelle.',
                    ]),
                ],
            ])
            ->add('registrationDeadline', DateType::class, [
                'widget' => 'single_text',
                'label' => "Date limite d'inscription",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une date limite d\'inscription',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(),
                        'message' => 'La date limite d\'inscription doit être ultérieure à la date actuelle.',
                    ]),
                ],
            ])
            ->add('numberPlaces', IntegerType::class, [
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le nombre de places doit être supérieur à zéro.',
                    ]),
                ],
            ])
            ->add('duration', IntegerType::class, [
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'La durée doit être supérieure à zéro.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une description',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'La description doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'La description doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'placeholder' => 'Campus',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un campus',
                    ]),
                ],
            ])
            ->add('outingImage', FileType::class, [
                'label' => "Image de l'évenement",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Choisissez une image valide (jpeg, png, ...)',
                    ]),
                ],
            ])
            ->add('namePlace', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une lieu',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le lieu doit faire au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Le lieu doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('street', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une rue',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'La rue doit faire au moins {{ limit }} caractères',
                        'max' => 150,
                        'maxMessage' => 'La rue doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('postalCode', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 1000,
                        'max' => 99999,
                        'notInRangeMessage' => 'Le code postal doit être compris entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une ville',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'La ville doit faire au moins {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'La ville doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
