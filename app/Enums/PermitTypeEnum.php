<?php

namespace App\Enums;

enum PermitTypeEnum: int
{
    case SICK = 1;
    case PAID_LEAVE = 2;
    case LEAVE = 3;
}
