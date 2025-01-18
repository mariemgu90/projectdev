<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email Address',
                'attr' => ['placeholder' => 'Enter your email']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'Client' => 'ROLE_CLIENT',
                    'Freelancer' => 'ROLE_FREELANCER',
                ],
                'multiple' => false,  // Allow only one selection
                'expanded' => true,   // Use radio buttons instead of a dropdown
            ])

            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => ['placeholder' => 'Enter a secure password']
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter your first name']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter your last name']
            ])
            ->add('companyName', TextType::class, [
            'label' => 'Company Name',
            'attr' => ['placeholder' => 'Enter your Company Name']
        ]);


        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function ($rolesAsArray) {
                // Transform the array to a comma-separated string
                return implode(', ', $rolesAsArray);
            },
            function ($rolesAsString) {
                // Transform the string back to an array
                return array_map('trim', explode(',', $rolesAsString));
            }
        ));


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
