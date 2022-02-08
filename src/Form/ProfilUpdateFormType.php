<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('campus', ChoiceType::class, [
                'label' => 'Campus :',
                'required' => false,
                'choices' => [
                    new Campus('Camp1'),
                    new Campus('Camp2'),
                    new Campus('Camp3'),
                ],
                'choice_value' => 'name'
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
