<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Enum\Difficulty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ],
                'attr' => ['placeholder' => 'Titre de la recette'],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [new NotBlank()],
                'attr' => ['placeholder' => 'Description de la recette', 'rows' => 4],
            ])
            ->add('prep_time', IntegerType::class, [
                'label' => 'Temps de préparation (min)',
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'attr' => ['min' => 1],
            ])
            ->add('cookTime', IntegerType::class, [
                'label' => 'Temps de cuisson (min)',
                'required' => false,
                'attr' => ['min' => 0],
            ])
            ->add('difficulty', EnumType::class, [
                'class' => Difficulty::class,
                'choice_label' => fn(Difficulty $d) => $d->getLabel(),
                'constraints' => [new NotBlank()],
            ])
            ->add('servings', IntegerType::class, [
                'label' => 'Nombre de portions',
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'attr' => ['min' => 1],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la recette',
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => 'image/*'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez choisir une image valide (JPEG, PNG, WebP, GIF).',
                        'maxSizeMessage' => 'L\'image ne peut pas dépasser 5 Mo.',
                    ]),
                ],
            ])
            ->add('recipeIngredients', CollectionType::class, [
                'entry_type' => RecipeIngredientType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('recipeSteps', CollectionType::class, [
                'entry_type' => RecipeStepType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
