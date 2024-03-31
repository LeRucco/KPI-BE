<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case DEVELOPER = 'DEVELOPER';
    case ADMIN = 'ADMIN';
    case EMPLOYEE = 'EMPLOYEE';
}
