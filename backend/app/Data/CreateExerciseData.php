<?php

namespace App\Data;

final class CreateExerciseData
{
    public function __construct(
        public readonly string $name,
        public readonly string $muscleGroup,
        public readonly ?string $description = null,
    ) {}
}
