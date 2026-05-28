<?php

namespace App\Data;

final class CreateWorkoutData
{
    public function __construct(
        public readonly ?int $workout_split_id = null,
        public readonly string $date,
        public readonly ?string $started_at = null,
        public readonly ?string $ended_at = null,
        public readonly ?string $notes = null,
    ) {}
}
