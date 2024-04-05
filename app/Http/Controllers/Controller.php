<?php

namespace App\Http\Controllers;

// use App\Traits\HttpResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Traits\HttpResponses;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller // extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HttpResponses;
    // use HttpResponses;
}
