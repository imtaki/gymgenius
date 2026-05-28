<?php

namespace App\Data;

final class UpdateWorkoutSplitExerciseData
{
    public function __construct(
        public readonly ?int $exercise_id = null,
        public readonly ?int $order = null,
        public readonly ?int $target_sets = null,
        public readonly ?int $target_reps = null,
        public readonly ?string $notes = null,
    ) {}
}
