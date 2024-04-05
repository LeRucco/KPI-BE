<?php

namespace App\Exceptions;

use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MyValidationException extends Exception
{
    use HttpResponses;

    public function report(Request $request)
    {
        Log::debug($request->all());
    }

    public function render(Request $request)
    {
        return $this->errorValidation([], Response::HTTP_UNPROCESSABLE_ENTITY, $this->getMessage());
    }
}
