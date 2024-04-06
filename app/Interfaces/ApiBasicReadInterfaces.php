<?php

namespace App\Interfaces;

interface ApiBasicReadInterfaces
{
    public function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder;
}
