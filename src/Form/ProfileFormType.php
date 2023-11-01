<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Campus;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer votre prénom',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre prénom doit faire au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Votre prénom doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer votre nom',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre nom doit faire au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Votre nom doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer votre email',
                    ]),
                    new Email([
                        'message' => 'Cet email n\'est pas valide.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre mail doit faire au moins {{ limit }} caractères',
                        'max' => 180,
                        'maxMessage' => 'Votre mail doit faire moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Votre mot de passe ne peut pas être plus long que {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Photo de profil',
                'required' => false,
                'mapped' => false,
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
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'placeholder' => 'Campus',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un campus',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
