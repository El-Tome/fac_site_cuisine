<?php

namespace App\Form;

use App\Entity\RecipeIngredients;
use App\Enum\Unit;
use App\Form\DataTransformer\IngredientToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

class RecipeIngredientType extends AbstractType
{
    public function __construct(private IngredientToIdTransformer $transformer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', HiddenType::class, [
                'attr' => ['class' => 'ingredient-id-field'],
                'constraints' => [new NotNull(message: 'Veuillez sélectionner un ingrédient.')],
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

        $builder->get('ingredient')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredients::class,
        ]);
    }
}
