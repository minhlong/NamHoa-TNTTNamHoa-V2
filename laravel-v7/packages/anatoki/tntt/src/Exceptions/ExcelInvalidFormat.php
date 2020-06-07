<?php

namespace TNTT\Exceptions;

use Exception;
use Throwable;

class ExcelInvalidFormat extends Exception
{
    protected $error = [
        'error' => [],
        'item'  => [],
    ];

    public function __construct($errors, $code = 0, Throwable $previous = null)
    {
        parent::__construct(null, $code, $previous);
        $this->error = $errors;
    }

    public function render()
    {
        return response()->json($this->error, 400);
    }
}
