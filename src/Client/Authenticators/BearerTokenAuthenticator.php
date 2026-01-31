<?php

namespace NinoHaar\Tempo\Client\Authenticators;

use GuzzleHttp\Psr7\Request;
use NinoHaar\Tempo\Configuration\ConfigurationInterface;
use NinoHaar\Tempo\Exceptions\AuthenticationException;

class BearerTokenAuthenticator
{
    public function __construct(private ConfigurationInterface $config)
    {
    }

    public function authenticate(Request $request): Request
    {
        $token = $this->config->getToken();

        if (! $token) {
            throw AuthenticationException::tokenNotConfigured();
        }

        return $request->withHeader('Authorization', 'Bearer '.$token);
    }
}
