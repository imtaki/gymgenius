<?php

namespace App\Data;

final class CreateWorkoutSplitExerciseData
{
    public function __construct(
        public readonly int $exercise_id,
        public readonly int $order = 0,
        public readonly ?int $target_sets = null,
        public readonly ?int $target_reps = null,
        public readonly ?string $notes = null,
    ) {}
}
