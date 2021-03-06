<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'required' => false
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom : ',
                'required' => false
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone : ',
                'required' => false
            ])
            ->add('email', TextType::class, [
                'label' => 'Email : ',
                'required' => false
            ])
            ->add('plainPassword', RepeatedType::class, [

                'label' => 'Mot de passe : ',
                'type' => PasswordType::class,
                'invalid_message' => 'Les 2 mots de passe doivent être identiques',
                'options' => ['attr' => ["class" => 'password-field']],
                'required' => false,
                'first_options' => ['label' => 'Mot de passe : '],
                'second_options' => ['label' => 'Répétez le mot de passe : '],

                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password']
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus : ',
                'required' => false,
                'class' => Campus::class,
                'choice_label' => 'name'
            ])
            ->add('newPhoto', FileType::class, [
                'label' => 'Photo : ',
                'mapped' => false,
                'required' => false
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
