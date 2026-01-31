<?php

namespace NinoHaar\Tempo\Configuration;

class DotEnvConfiguration extends AbstractConfiguration
{
    public function __construct()
    {
        // Load configuration from Laravel config (which reads from .env)
        $this->config = config('tempo', []);
    }
}
