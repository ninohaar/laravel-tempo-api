<?php

namespace NinoHaar\Tempo\Exceptions;

class NotFoundException extends TempoException
{
    public static function resource(string $resource, string $identifier): self
    {
        return new self("Tempo {$resource} not found: {$identifier}");
    }
}
