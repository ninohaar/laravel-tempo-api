<?php

namespace NinoHaar\Tempo\Exceptions;

use Exception;

class TempoException extends Exception
{
    /**
     * Create a new Tempo Exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
