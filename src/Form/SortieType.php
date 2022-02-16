<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Ville;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie : '
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date et heure de la sortie : ',
                'widget' => 'single_text',
                'html5' => true

            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('nbInscriptionMax', TextType::class, [
                'label' => 'Nombre de places : ',
                //'constraints' => new Regex('[0-9]')
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée : '
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos : '
            ])
            ->add('ville', TextType::class, [
                'mapped' => false,
                'label' => 'Ville : ',
                'attr' => ['autocomplete' => 'off', 'list' => 'list_villes']
            ])
            ->add ('codePostal', ChoiceType::class, [
                'label' => 'Code Postal : ',
                'mapped' => false,
                'placeholder' => "---"
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu : ',
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'required' => false
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue : ',
                'mapped' => false,
                'attr' => ['autocomplete' => 'off', 'list' => 'list_rues']
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude : ',
                'mapped' => false,
                'disabled' => true
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude : ',
                'mapped' => false,
                'disabled' => true
            ])
        ;

        $formModifier = function (FormInterface $form, Ville $ville = null) {
            $zipcodes = (null === $ville) ? [] : $ville->getCodePostal();
            $form -> add('codePostal', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'codePostal',
                'choices' => [
                    'Choisissez un code postal' => null,
                    $zipcodes
                ],
                'placeholder' => 'Code Postal (choisir ville)',
                'label' => 'Code Postal : ',
                'mapped' => false
            ]);
        };

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                $ville = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $ville);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'attr' => ['id' => 'sortie_form',
                'novalidate' => 'novalidate'],

        ]);
    }
}
