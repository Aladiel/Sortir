<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('campus', EntityType::class, [
                'label' => 'Campus :',
                'required' => false,
                'class' => Campus::class,
                'choice_label' => 'name'
            ])

            ->add('nom', TextType::class, [
                'label' => 'Le nom contient :',
                'required' => false,
                'attr' => [
                    'placeholder' => ''
                ]
            ])

        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'Method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}