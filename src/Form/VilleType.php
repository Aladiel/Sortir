<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codePostal', TextType::class, [
                'label' => 'Code Postal'
            ])
            ->add('nom', EntityType::class, [
                'label' => 'Ville',
                'class' => Ville::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
            'attr' => ['id' => 'ville_form'],
        ]);
    }
}
