<?php

namespace NinoHaar\Tempo\Exceptions;

class AuthenticationException extends TempoException
{
    public static function invalidCredentials(): self
    {
        return new self('Invalid Tempo API credentials. Please check your authentication configuration.');
    }

    public static function tokenNotConfigured(): self
    {
        return new self('Tempo API token not configured. Set TEMPO_TOKEN in your .env file.');
    }

    public static function oauth2NotConfigured(): self
    {
        return new self('Tempo OAuth2 credentials not configured. Set TEMPO_CLIENT_ID and TEMPO_CLIENT_SECRET.');
    }

    public static function refreshDisabled(): self
    {
        return new self(
            'OAuth2 token refresh is disabled due to previous refresh failure. '
            . 'Run "php artisan tempo:oauth-refresh" to manually refresh the token.'
        );
    }
}
