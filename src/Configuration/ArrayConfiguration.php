<?php

namespace NinoHaar\Tempo\Configuration;

class ArrayConfiguration extends AbstractConfiguration
{
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }
}
