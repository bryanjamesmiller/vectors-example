<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentType: string
{
    case Tuition = 'tuition';
    case LabFees = 'lab_fees';
    case Tools = 'tools';
    case RoomAndBoard = 'room_and_board';
}
