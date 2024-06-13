<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Écrire votre avis ici'],
            ]) 
            ->add('note', ChoiceType::class, [
                
                'label' => 'Évaluer :',

                'choices' => [ // pour que mon input devienne un Select
                    '1' => '1',
                    '2' => '2',
                    '3'=> '3',
                    "4" => "4",
                    '5' => '5'
                ],
                'choice_attr' => [
                    'évaluer' => ['disabled' => true] // pour qu'on ne puisse pas sélectionner cette valeur qui sert de placeholder
                ],
            ])

            // ->add('date', null, [
            //     'widget' => 'single_text',
            // ])
           
            // ->add('valid')
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
