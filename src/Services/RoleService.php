<?php

namespace NinoHaar\Tempo\Services;

class RoleService extends BaseService
{
    public function list(array $params = []): array
    {
        return $this->cache->remember(
            $this->cacheKey('roles'),
            fn () => $this->client->get('roles', $params),
            3600
        );
    }

    public function get(int $id): ?array
    {
        return $this->client->get("roles/{$id}");
    }

    public function create(array $data): array
    {
        return $this->client->post('roles', $data);
    }

    public function update(int $id, array $data): array
    {
        return $this->client->put("roles/{$id}", $data);
    }

    public function delete(int $id): bool
    {
        $this->client->delete("roles/{$id}");
        return true;
    }

    public function default(): ?array
    {
        return $this->client->get('roles/default');
    }
}
