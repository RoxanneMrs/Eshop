<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom'],
            ])

            ->add('name', TextType::class, [
                'label'=> 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
            ])

            ->add('topic', ChoiceType::class, [
                
                'label' => 'Quel est le sujet de votre message ?',

                'choices' => [ // pour que mon input devienne un Select
                    'Motif de contact' => NULL,
                    'Je souhaiterais passer une commande personnalisée' => 'Je souhaiterais passer une commande personnalisée',
                    'Il y a un problème avec ma commande'=> 'Il y a un problème avec ma commande',
                    "J'aimerais plus d'informations" => "J'aimerais plus d'informations",
                    'Autre' => 'Autre'
                ],
                'choice_attr' => [
                    'Motif de contact' => ['disabled' => true] // pour qu'on ne puisse pas sélectionner cette valeur qui sert de placeholder
                ],
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['placeholder' => 'Votre message']
            ])

            // ->add('date', null, [
            //     'widget' => 'single_text',
            // ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Votre email'],
            ])

            ->add('picture', FileType::class, [
                'label' => 'Image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier de type pgn, jpg ou jpeg',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
