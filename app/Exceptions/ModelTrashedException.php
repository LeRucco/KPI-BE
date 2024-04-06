<?php

namespace App\Exceptions;

use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ModelTrashedException extends Exception
{
    use HttpResponses;

    public static function alreadySoftDeleted(): ModelTrashedException
    {
        return new self('Already soft deleted', Response::HTTP_OK);
    }

    public static function stillExist(): ModelTrashedException
    {
        return new self('Data still exists', Response::HTTP_OK);
    }

    public function report(Request $request)
    {
        Log::debug($request->all());
    }

    public function render(Request $request)
    {
        return $this->success([], $this->getCode(), $this->getMessage());
    }
}
