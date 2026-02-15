<?php

namespace App\Exceptions;

use RuntimeException;

class ExternalDataException extends RuntimeException
{
    public function __construct(string $message = 'External data error', int $code = 502)
    {
        parent::__construct($message, $code);
    }
}
