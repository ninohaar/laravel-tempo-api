<?php

namespace NinoHaar\Tempo\Exceptions;

class ValidationException extends TempoException
{
    public static function invalidParameter(string $parameter, string $reason): self
    {
        return new self("Invalid parameter '{$parameter}': {$reason}");
    }

    public static function missingRequiredParameter(string $parameter): self
    {
        return new self("Missing required parameter: {$parameter}");
    }
}
