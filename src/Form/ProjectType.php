<?php
namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('title', TextType::class, [
'label' => 'Titre du projet',
'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le titre'],
])
->add('description', TextareaType::class, [
'label' => 'Description',
'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Entrez la description'],
])
->add('criteria', TextareaType::class, [
'label' => 'Critères',
'attr' => ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Décrivez les critères'],
])
->add('budget', MoneyType::class, [
'label' => 'Budget (€)',
'currency' => 'EUR',
'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le budget'],
])
->add('deadline', DateTimeType::class, [
'widget' => 'single_text',
'label' => 'Date limite',
'attr' => ['class' => 'form-control', 'placeholder' => 'Sélectionnez une date'],
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Project::class,  // Toujours lié à l'entité Project
]);
}
}
