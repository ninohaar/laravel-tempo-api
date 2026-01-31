<?php

namespace NinoHaar\Tempo\Services;

class ConfigurationService extends BaseService
{
    public function globalConfiguration(): array
    {
        return $this->cache->remember(
            $this->cacheKey('global_config'),
            fn () => $this->client->get('globalconfiguration'),
            7200
        );
    }

    public function periods(array $params = []): array
    {
        return $this->cache->remember(
            $this->cacheKey('periods', $params),
            fn () => $this->client->get('periods', $params),
            7200
        );
    }

    public function holidaySchemes(array $params = []): array
    {
        return $this->cache->remember(
            $this->cacheKey('holiday_schemes'),
            fn () => $this->client->get('holiday-schemes', $params),
            3600
        );
    }

    public function getHolidayScheme(int $id): ?array
    {
        return $this->client->get("holiday-schemes/{$id}");
    }

    public function createHolidayScheme(array $data): array
    {
        return $this->client->post('holiday-schemes', $data);
    }

    public function workloadSchemes(array $params = []): array
    {
        return $this->cache->remember(
            $this->cacheKey('workload_schemes'),
            fn () => $this->client->get('workload-schemes', $params),
            3600
        );
    }

    public function getWorkloadScheme(int $id): ?array
    {
        return $this->client->get("workload-schemes/{$id}");
    }

    public function createWorkloadScheme(array $data): array
    {
        return $this->client->post('workload-schemes', $data);
    }

    public function userSchedule(?string $accountId = null): array
    {
        $endpoint = $accountId ? "user-schedule/{$accountId}" : 'user-schedule';
        return $this->client->get($endpoint);
    }
}
