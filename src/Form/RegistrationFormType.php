<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email', EmailType::class, [
                'label' => "Email",
                'attr' => ['placeholder' => 'Votre email'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre email doit être renseigné']),
                    new Email(['message' => 'Veuillez renseigner un email valide!']),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'label' => "Accepter les conditions",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions.',
                    ]),
                ],
            ])

            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Mot de passe",
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter un minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('picture', FileType::class, [
                'label' => 'Image de profil',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '4024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier de type pgn ou jpeg',
                    ])
                ],
            ])

            ->add('firstName', TextType::class, [
                'label' => "Prénom",
                'attr' => ['placeholder' => 'Votre prénom'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre prénom doit être renseigné']),
                    new Length(['min' => 2, 'minMessage' => 'Votre prénom doit comporter un minimum de 2 caractères'])
                ],
            ])

            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre nom doit être renseigné']),
                    new Length(['min' => 2, 'minMessage' => 'Votre nom doit comporter un minimum de 2 caractères' ])],
            ])

            ->add('phoneNumber', TelType::class, [
                'label' => "Numéro de téléphone",
                'attr' => ['placeholder' => 'Votre numéro de téléphone'],
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez ajouter un numéro de téléphone']),
                    new Length(['min' => 10, 'minMessage' => 'Votre numéro de téléphone doit être composé de 10 chiffres',
                                'max' => 10, 'maxMessage' => 'Votre numéro de téléphone doit être composé de 10 chiffres'])
                ],
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Votre adresse'],
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Votre adresse doit être renseignée']),],
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['placeholder' => 'Votre ville'],
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Votre ville doit être renseignée']),],
            ])

            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr' => ['placeholder' => 'Votre pays'],
                'required' => true,
            ])

            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Votre code postal'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre code postal doit être renseigné']),
                    new Length(['min' => 5, 'minMessage' => 'Votre code postal doit comporter 5 caractères',
                                'max' => 5, 'maxMessage' => 'Votre code postal doit comporter 5 caractères'])],                                 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
