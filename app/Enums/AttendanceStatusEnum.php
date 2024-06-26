<?php

namespace App\Enums;

enum AttendanceStatusEnum: int
{
    case WAITING = 1;
    case APPROVE = 2;
    case REJECT = 3;
}
