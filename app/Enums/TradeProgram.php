<?php

declare(strict_types=1);

namespace App\Enums;

enum TradeProgram: string
{
    case Electrical = 'Electrical';
    case Welding = 'Welding';
    case Hvac = 'HVAC';
    case Plumbing = 'Plumbing';
    case Automotive = 'Automotive';
    case Carpentry = 'Carpentry';
}
