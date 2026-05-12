<?php

namespace App\Enum;

enum Difficulty: string
{
    case Easier = 'easier';
    case Easy   = 'easy';
    case Medium = 'medium';
    case Hard   = 'hard';
    case Harder = 'harder';

    public function getLabel(): string
    {
        return match($this) {
            self::Easier => 'Très facile',
            self::Easy   => 'Facile',
            self::Medium => 'Médium',
            self::Hard   => 'Dur',
            self::Harder => 'Très dur',
        };
    }
}

