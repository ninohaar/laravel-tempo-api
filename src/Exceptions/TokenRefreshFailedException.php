<?php

namespace NinoHaar\Tempo\Exceptions;

class TokenRefreshFailedException extends TempoException
{
    public static function fromResponse(array $response): self
    {
        $errorDescription = $response['error_description'] ?? $response['error'] ?? 'Unknown error';
        return new self(
            "OAuth2 token refresh failed: {$errorDescription}. "
            . "Auto-refresh is disabled. Run 'php artisan tempo:oauth-refresh' to retry manually."
        );
    }
}
