<?php

namespace App\Data;

final class CreateMealData
{
    public function __construct(
        public readonly string $name,
        public readonly string $category,
        public readonly int $calories,
        public readonly int $protein,
        public readonly int $carbs,
        public readonly int $fats,
        public readonly ?string $date = null,
    ) {}
}
