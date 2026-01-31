<?php

namespace NinoHaar\Tempo\Configuration;

use NinoHaar\Tempo\Exceptions\ValidationException;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    protected array $config = [];

    public function getBaseUrl(): string
    {
        $baseUrl = $this->config['base_url'] ?? env('TEMPO_BASE_URL', 'https://api.tempo.io');
        return rtrim($baseUrl, '/');
    }

    public function getApiVersion(): string
    {
        return $this->config['api_version'] ?? env('TEMPO_API_VERSION', '4');
    }

    public function getAuthType(): string
    {
        $type = $this->config['auth']['type'] ?? env('TEMPO_AUTH_TYPE', 'token');
        if (! in_array($type, ['token', 'oauth2'], true)) {
            throw ValidationException::invalidParameter('auth_type', 'Must be "token" or "oauth2"');
        }
        return $type;
    }

    public function getToken(): ?string
    {
        return $this->config['auth']['token']['bearer'] ?? env('TEMPO_TOKEN');
    }

    public function getOAuth2ClientId(): ?string
    {
        return $this->config['auth']['oauth2']['client_id'] ?? env('TEMPO_CLIENT_ID');
    }

    public function getOAuth2ClientSecret(): ?string
    {
        return $this->config['auth']['oauth2']['client_secret'] ?? env('TEMPO_CLIENT_SECRET');
    }

    public function isOAuth2RefreshDisabled(): bool
    {
        $disabled = $this->config['auth']['oauth2']['refresh_disabled'] ?? env('TEMPO_OAUTH_REFRESH_DISABLED', false);
        return filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    }

    public function setOAuth2RefreshDisabled(bool $disabled): void
    {
        $this->config['auth']['oauth2']['refresh_disabled'] = $disabled;
    }

    public function getHttpTimeout(): int
    {
        return (int) ($this->config['http']['timeout'] ?? env('TEMPO_TIMEOUT', 30));
    }

    public function getHttpConnectTimeout(): int
    {
        return (int) ($this->config['http']['connect_timeout'] ?? 10);
    }

    public function getRetryTimes(): int
    {
        return (int) ($this->config['http']['retry']['times'] ?? env('TEMPO_RETRY_TIMES', 3));
    }

    public function getRetrySleep(): int
    {
        return (int) ($this->config['http']['retry']['sleep'] ?? env('TEMPO_RETRY_SLEEP', 1000));
    }

    public function isCachingEnabled(): bool
    {
        return filter_var(
            $this->config['cache']['enabled'] ?? env('TEMPO_CACHE_ENABLED', false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function getCacheTtl(): int
    {
        return (int) ($this->config['cache']['default_ttl'] ?? env('TEMPO_CACHE_TTL', 3600));
    }

    public function getWebhookSecret(): ?string
    {
        return $this->config['webhooks']['signature_secret'] ?? env('TEMPO_WEBHOOK_SECRET');
    }

    public function isLoggingEnabled(): bool
    {
        return filter_var(
            $this->config['logging']['enabled'] ?? env('TEMPO_LOG_ENABLED', false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function getLogChannel(): string
    {
        return $this->config['logging']['channel'] ?? env('TEMPO_LOG_CHANNEL', 'single');
    }

    public function getLogLevel(): string
    {
        return $this->config['logging']['level'] ?? env('TEMPO_LOG_LEVEL', 'info');
    }
}
