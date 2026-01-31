<?php

namespace NinoHaar\Tempo\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as BaseTestCase;
use NinoHaar\Tempo\TempoServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set default config for tests
        Config::set('tempo', [
            'base_url' => 'https://api.tempo.io',
            'api_version' => '4',
            'auth' => [
                'type' => 'token',
                'token' => ['bearer' => 'test-token'],
            ],
            'http' => [
                'timeout' => 30,
                'connect_timeout' => 10,
                'retry' => ['times' => 3, 'sleep' => 100],
            ],
            'cache' => ['enabled' => false],
            'webhooks' => [
                'signature_secret' => 'test-secret',
                'route_path' => '/api/webhooks/tempo',
            ],
            'logging' => ['enabled' => false],
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            TempoServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Tempo' => 'NinoHaar\Tempo\Facades\Tempo',
        ];
    }
}
