<?php

namespace App\Enums;

enum CalenderColorEnum: string
{
    case ATTEND = 'lightblue';
    case LATE = 'red';
    case EARLY_LEAVE = 'purple';
    case ALPHA = 'gray';
    case SICK_OR_LEAVE = 'yellow';
    case PAID_LEAVE = 'green';
}
