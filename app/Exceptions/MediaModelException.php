<?php

namespace App\Exceptions;

use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MediaModelException extends Exception
{
    use HttpResponses;

    public static function uuidNotFound(): MediaModelException
    {
        return new self('Uuid Media Model Not Found', Response::HTTP_BAD_REQUEST);
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
