<?php

namespace NinoHaar\Tempo\Services;

class PermissionRoleService extends BaseService
{
    public function list(array $params = []): array
    {
        return $this->cache->remember(
            $this->cacheKey('permission_roles'),
            fn () => $this->client->get('permission-roles', $params),
            3600
        );
    }

    public function globalRoles(): array
    {
        return $this->client->get('permission-roles/global');
    }

    public function get(int $id): ?array
    {
        return $this->client->get("permission-roles/{$id}");
    }

    public function create(array $data): array
    {
        return $this->client->post('permission-roles', $data);
    }

    public function update(int $id, array $data): array
    {
        return $this->client->put("permission-roles/{$id}", $data);
    }

    public function delete(int $id): bool
    {
        $this->client->delete("permission-roles/{$id}");
        return true;
    }
}
