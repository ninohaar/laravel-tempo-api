<?php

namespace NinoHaar\Tempo\Services;

class ProjectService extends BaseService
{
    /**
     * List projects.
     * Requires: Financial Manager
     */
    public function list(array $params = []): array
    {
        return $this->cache->remember(
            $this->cacheKey('projects', $params),
            fn () => $this->client->get('projects', $params),
            1800
        );
    }

    public function get(int $id): ?array
    {
        return $this->client->get("projects/{$id}");
    }

    public function create(array $data): array
    {
        return $this->client->post('projects', $data);
    }

    public function update(int $id, array $data): array
    {
        return $this->client->put("projects/{$id}", $data);
    }

    public function scope(int $id): ?array
    {
        return $this->client->get("projects/{$id}/scope");
    }

    public function scopeTasks(int $id): array
    {
        return $this->client->get("projects/{$id}/scope/tasks");
    }

    public function timeframe(int $id): ?array
    {
        return $this->client->get("projects/{$id}/timeframe");
    }

    public function updateTimeframe(int $id, array $data): array
    {
        return $this->client->put("projects/{$id}/timeframe", $data);
    }

    public function deleteTimeframe(int $id): bool
    {
        $this->client->delete("projects/{$id}/timeframe");
        return true;
    }

    public function teamMembers(int $id): array
    {
        return $this->client->get("projects/{$id}/team-members");
    }

    public function teamMemberRates(int $projectId, int $memberId): array
    {
        return $this->client->get("projects/{$projectId}/team-members/{$memberId}/rates");
    }

    public function budget(int $id): ?array
    {
        return $this->client->get("projects/{$id}/budget");
    }

    public function setBudget(int $id, array $data): array
    {
        return $this->client->post("projects/{$id}/budget", $data);
    }

    public function deleteBudget(int $id): bool
    {
        $this->client->delete("projects/{$id}/budget");
        return true;
    }

    public function expenses(int $id, array $params = []): array
    {
        return $this->client->get("projects/{$id}/expenses", $params);
    }

    public function createExpense(int $id, array $data): array
    {
        return $this->client->post("projects/{$id}/expenses", $data);
    }
}
