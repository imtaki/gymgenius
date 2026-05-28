<?php

namespace App\Data;

final class CreateWorkoutSplitData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}
}
