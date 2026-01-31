<?php

namespace NinoHaar\Tempo\Client\Authenticators;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use League\OAuth2\Client\Provider\GenericProvider;
use NinoHaar\Tempo\Client\EnvFileUpdater;
use NinoHaar\Tempo\Configuration\ConfigurationInterface;
use NinoHaar\Tempo\Exceptions\AuthenticationException;
use NinoHaar\Tempo\Exceptions\TokenRefreshFailedException;

class OAuth2Authenticator
{
    private ?string $accessToken = null;

    private ?int $tokenExpiresAt = null;

    public function __construct(
        private ConfigurationInterface $config,
        private EnvFileUpdater $envUpdater,
    ) {
        $this->accessToken = env('TEMPO_OAUTH2_ACCESS_TOKEN');
        $expiresAt = env('TEMPO_OAUTH2_TOKEN_EXPIRES_AT');
        $this->tokenExpiresAt = $expiresAt ? (int) $expiresAt : null;
    }

    public function authenticate(Request $request): Request
    {
        if ($this->config->isOAuth2RefreshDisabled()) {
            throw AuthenticationException::refreshDisabled();
        }

        // Refresh token if expired
        if ($this->isTokenExpired()) {
            $this->refreshAccessToken();
        }

        if (! $this->accessToken) {
            throw AuthenticationException::oauth2NotConfigured();
        }

        return $request->withHeader('Authorization', 'Bearer '.$this->accessToken);
    }

    private function isTokenExpired(): bool
    {
        return $this->tokenExpiresAt === null || time() >= $this->tokenExpiresAt;
    }

    public function refreshAccessToken(): void
    {
        try {
            $provider = new GenericProvider([
                'clientId' => $this->config->getOAuth2ClientId(),
                'clientSecret' => $this->config->getOAuth2ClientSecret(),
                'redirectUri' => env('TEMPO_OAUTH2_REDIRECT_URI', 'http://localhost'),
                'urlAuthorize' => 'https://api.tempo.io/oauth/authorize/redirect',
                'urlAccessToken' => 'https://api.tempo.io/oauth/token/',
                'urlResourceOwnerDetails' => 'https://api.tempo.io/4/myself',
            ]);

            // Get refresh token from env or try to get new access token with client credentials
            $refreshToken = env('TEMPO_OAUTH2_REFRESH_TOKEN');

            if ($refreshToken) {
                $accessToken = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $refreshToken,
                ]);
            } else {
                // Try client credentials flow if no refresh token available
                $accessToken = $provider->getAccessToken('client_credentials', [
                    'scope' => 'api',
                ]);
            }

            $this->accessToken = $accessToken->getToken();
            $this->tokenExpiresAt = $accessToken->getExpires();

            // Update .env file with new tokens
            $this->envUpdater->update([
                'TEMPO_OAUTH2_ACCESS_TOKEN' => $this->accessToken,
                'TEMPO_OAUTH2_TOKEN_EXPIRES_AT' => $this->tokenExpiresAt,
            ]);

            if ($newRefreshToken = $accessToken->getRefreshToken()) {
                $this->envUpdater->update([
                    'TEMPO_OAUTH2_REFRESH_TOKEN' => $newRefreshToken,
                ]);
            }
        } catch (\Throwable $e) {
            // Disable auto-refresh on failure
            $this->config->setOAuth2RefreshDisabled(true);
            $this->envUpdater->update([
                'TEMPO_OAUTH_REFRESH_DISABLED' => 'true',
            ]);

            throw TokenRefreshFailedException::fromResponse([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
