<?php

namespace App\Enums;

enum PermitTypeEnum: int
{
    case SICK = 1;          // Sakit
    case PAID_LEAVE = 2;    // Cuti
    case LEAVE = 3;         // Izin
}
