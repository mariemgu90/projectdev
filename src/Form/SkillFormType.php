<?php

// src/Form/SkillFormType.php

namespace App\Form;
use App\Entity\User;
use App\Entity\Skill;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skillName', null, [
                'label' => 'Skill Name',
                'attr' => ['placeholder' => 'Enter a skill'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);
    }


}
