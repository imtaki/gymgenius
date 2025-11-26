<?php

namespace App\Enums;

enum GoalType: string
{
    case CUTTING = 'cutting';
    case MAINTAIN_WEIGHT = 'maintaining';
    case BULKING = 'bulking';
}
