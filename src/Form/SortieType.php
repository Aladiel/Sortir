<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie '
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date et heure de la sortie ',
                'widget' => 'single_text',
                'html5' => true

            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('nbInscriptionMax', TextType::class, [
                'label' => 'Nombre de places',
                'constraints' => new Regex('[0-9]')
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'attr' => ['id' => 'sortie_form'],
        ]);
    }
}
