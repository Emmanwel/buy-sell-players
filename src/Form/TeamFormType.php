<?php

namespace App\Form;

use App\Entity\Team;
use App\Form\PlayerFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TeamFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('team_name', TextType::class, [
                'attr' => array(
                    'class' => ' block mt-10 w-full p-4 mb-6 text-3xl outline-none text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                    ', 'placeholder' => 'Team Name'

                ),
                'label' => false
            ])

            ->add('league', TextType::class, [
                'attr' => array(
                    'class' => 'block mt-10 w-full p-4 mb-6 text-3xl outline-none text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-md focus:ring-blue-500 focus:border-blue-500', 'placeholder' => 'Team Country'

                ),
                'label' => false
            ])
            ->add('releaseYear', NumberType::class, [
                'attr' => array(
                    'class' => 'block mt-10 w-full p-4 mb-6 text-3xl outline-none text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-md focus:ring-blue-500 focus:border-blue-500   ', 'placeholder' => 'Money Balance '
                ),
                'label' => false,
                'scale' => 2,
            ])

            ->add(
                'team_logo',
                FileType::class,

                [
                    'attr' => array(
                        'class' => 'mb-8 block w-10% text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50  focus:outline-none '
                    ),

                    'mapped' => false,
                    'required' => true,
                ]
            )


            ->add('players', CollectionType::class, [

                'entry_type' => PlayerFormType::class,

                'attr' => array(
                    'class' => 'player-index text-black bg-slate-200 mb-10 mt-10 block border-b-2 w-2/3 h-auto text-4xl outline-none',

                ),

                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__player_name__',
                'by_reference' => false,
                // 'label' => false,



            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
