<?php

namespace NinoHaar\Tempo\Configuration;

interface ConfigurationInterface
{
    public function getBaseUrl(): string;

    public function getApiVersion(): string;

    public function getAuthType(): string;

    public function getToken(): ?string;

    public function getOAuth2ClientId(): ?string;

    public function getOAuth2ClientSecret(): ?string;

    public function isOAuth2RefreshDisabled(): bool;

    public function setOAuth2RefreshDisabled(bool $disabled): void;

    public function getHttpTimeout(): int;

    public function getHttpConnectTimeout(): int;

    public function getRetryTimes(): int;

    public function getRetrySleep(): int;

    public function isCachingEnabled(): bool;

    public function getCacheTtl(): int;

    public function getWebhookSecret(): ?string;

    public function isLoggingEnabled(): bool;

    public function getLogChannel(): string;

    public function getLogLevel(): string;
}
