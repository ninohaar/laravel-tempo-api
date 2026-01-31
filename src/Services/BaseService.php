<?php

namespace NinoHaar\Tempo\Services;

use NinoHaar\Tempo\Client\CacheDecorator;
use NinoHaar\Tempo\Client\TempoClient;

abstract class BaseService
{
    protected const CACHE_TTL = 3600;

    public function __construct(
        protected CacheDecorator $cache,
        protected TempoClient $client,
    ) {
    }

    /**
     * Get cache key for an operation.
     */
    protected function cacheKey(string $operation, array $params = []): string
    {
        $paramString = implode('_', array_map(fn ($k, $v) => "{$k}:{$v}", array_keys($params), $params));
        return "{$operation}" . ($paramString ? ":{$paramString}" : '');
    }
}
