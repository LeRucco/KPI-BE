<?php

namespace App\Enums;

enum PermitStatusEnum: int
{
    case WAITING = 1;
    case APPROVE = 2;
    case REJECT = 3;
}
