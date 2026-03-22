<?php

namespace App\Data;

final class UpdateMealData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $category = null,
        public readonly ?int $calories = null,
        public readonly ?int $protein = null,
        public readonly ?int $carbs = null,
        public readonly ?int $fats = null,
        public readonly ?string $date = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            category: $data['category'] ?? null,
            calories: $data['calories'] ?? null,
            protein: $data['protein'] ?? null,
            carbs: $data['carbs'] ?? null,
            fats: $data['fats'] ?? null,
            date: $data['date'] ?? null,
        );
    }
}
