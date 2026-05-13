<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredients;
use App\Enum\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un ingrédient',
                'constraints' => [new NotBlank()],
            ])
            ->add('quantity', NumberType::class, [
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'attr' => ['placeholder' => 'Quantité', 'step' => '0.01'],
            ])
            ->add('unit', EnumType::class, [
                'class' => Unit::class,
                'choice_label' => fn(Unit $u) => $u->getLabel(),
                'constraints' => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredients::class,
        ]);
    }
}
