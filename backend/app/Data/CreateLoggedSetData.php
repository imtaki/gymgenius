<?php

namespace App\Data;

final class CreateLoggedSetData
{
    public function __construct(
        public readonly int $workout_split_exercise_id,
        public readonly int $set_number,
        public readonly ?int $reps = null,
        public readonly ?float $weight = null,
        public readonly ?int $rpe = null,
    ) {}
}
