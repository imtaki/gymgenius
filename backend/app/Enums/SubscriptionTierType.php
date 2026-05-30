<?php

namespace App\Enums;

enum SubscriptionTierType: string
{
    case FREE = 'free';
    case PRO = 'pro';
    case PRO_PLUS = 'pro_plus';
}
