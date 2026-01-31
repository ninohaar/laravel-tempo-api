<?php

namespace NinoHaar\Tempo\Exceptions;

class WebhookValidationException extends TempoException
{
    public static function invalidSignature(): self
    {
        return new self('Webhook signature validation failed. Possible tampering detected.');
    }

    public static function missingSignature(): self
    {
        return new self('Webhook signature header missing. Cannot validate request authenticity.');
    }

    public static function secretNotConfigured(): self
    {
        return new self('Webhook secret not configured. Set TEMPO_WEBHOOK_SECRET in your .env file.');
    }
}
