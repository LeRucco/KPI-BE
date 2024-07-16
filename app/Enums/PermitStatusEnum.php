<?php

namespace App\Enums;

enum PermitStatusEnum: int
{
    case WAITING = 1;
    case APPROVE = 2;
    case REJECT = 3;

    public function indonesia(): string
    {
        return match ($this) {
            PermitStatusEnum::WAITING => 'Menunggu',
            PermitStatusEnum::APPROVE => 'Diterima',
            PermitStatusEnum::REJECT => 'Ditolak',
        };
    }
}
