<?php

namespace App\Form\DataTransformer;

use App\Repository\IngredientRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IngredientToIdTransformer implements DataTransformerInterface
{
    public function __construct(private IngredientRepository $repository) {}

    public function transform(mixed $value): mixed
    {
        return $value?->getId() ?? '';
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (empty($value)) {
            return null;
        }

        $ingredient = $this->repository->find((int) $value);

        if (!$ingredient) {
            throw new TransformationFailedException("Ingrédient introuvable.");
        }

        return $ingredient;
    }
}
