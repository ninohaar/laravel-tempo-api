<?php

namespace NinoHaar\Tempo\Exceptions;

class RateLimitException extends TempoException
{
    public function __construct(
        private int $retryAfter = 60,
        string $message = 'Tempo API rate limit exceeded.',
    ) {
        parent::__construct($message);
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
