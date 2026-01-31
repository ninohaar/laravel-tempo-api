<?php

namespace NinoHaar\Tempo\Client;

use Illuminate\Support\Facades\Cache;
use NinoHaar\Tempo\Configuration\ConfigurationInterface;

class CacheDecorator
{
    public function __construct(
        private TempoClient $client,
        private ConfigurationInterface $config,
    ) {
    }

    /**
     * Get from cache or execute the callable.
     *
     * @param  string  $key  Cache key identifier
     * @param  callable  $callback  Callable to execute if not cached
     * @param  int|null  $ttl  Time-to-live in seconds (uses config default if null)
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        if (! $this->config->isCachingEnabled()) {
            return call_user_func($callback);
        }

        $cacheKey = $this->buildCacheKey($key);
        $ttl ??= $this->config->getCacheTtl();

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Get from cache.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (! $this->config->isCachingEnabled()) {
            return $default;
        }

        $cacheKey = $this->buildCacheKey($key);
        return Cache::get($cacheKey, $default);
    }

    /**
     * Put value in cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int|null  $ttl
     */
    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        if (! $this->config->isCachingEnabled()) {
            return;
        }

        $cacheKey = $this->buildCacheKey($key);
        $ttl ??= $this->config->getCacheTtl();

        Cache::put($cacheKey, $value, $ttl);
    }

    /**
     * Forget cache entry.
     *
     * @param  string  $key
     */
    public function forget(string $key): void
    {
        if (! $this->config->isCachingEnabled()) {
            return;
        }

        $cacheKey = $this->buildCacheKey($key);
        Cache::forget($cacheKey);
    }

    /**
     * Build cache key with token info for multi-user safety.
     *
     * Includes hashed OAuth2 token or account scope to prevent
     * data collision between different authenticated users.
     */
    private function buildCacheKey(string $key): string
    {
        $prefix = 'tempo_';
        $tokenInfo = $this->getTokenInfo();

        return $prefix.$tokenInfo.'_'.$key;
    }

    /**
     * Get token info hash for cache key uniqueness.
     */
    private function getTokenInfo(): string
    {
        $authType = $this->config->getAuthType();

        if ($authType === 'oauth2') {
            $token = env('TEMPO_OAUTH2_ACCESS_TOKEN', '');
            $account = env('TEMPO_OAUTH2_ACCOUNT', '');

            return hash('sha256', $account.'_'.$token);
        }

        // Bearer token
        $token = $this->config->getToken() ?? '';
        return hash('sha256', $token);
    }

    /**
     * Get the underlying TempoClient instance.
     */
    public function getClient(): TempoClient
    {
        return $this->client;
    }
}
