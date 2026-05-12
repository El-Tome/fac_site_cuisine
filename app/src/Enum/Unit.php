<?php

namespace App\Enum;

enum Unit: string
{
    case Milliliter = 'ml';
    case Centiliter = 'cl';
    case Liter      = 'l';
    case Milligram  = 'mg';
    case Gram       = 'g';
    case Kilogram   = 'kg';
    case Teaspoon   = 'tsp';
    case Tablespoon = 'tbsp';
    case Cup        = 'cup';
    case Piece      = 'piece';
    case Pinch      = 'pinch';
    case Dash       = 'dash';

    public function getLabel(): string
    {
        return match($this) {
            self::Milliliter => 'Millilitres (ml)',
            self::Centiliter => 'Centilitres (cl)',
            self::Liter      => 'Litres (l)',
            self::Milligram  => 'Milligrammes (mg)',
            self::Gram       => 'Grammes (g)',
            self::Kilogram   => 'Kilogrammes (kg)',
            self::Teaspoon   => 'Cuillère à café (tsp)',
            self::Tablespoon => 'Cuillère à soupe (tbsp)',
            self::Cup        => 'Tasses (cup)',
            self::Piece      => 'Pièces',
            self::Pinch      => 'Pincées',
            self::Dash       => 'Traits',
        };
    }
}
