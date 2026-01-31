<?php

namespace NinoHaar\Tempo\Services;

class PlanService extends BaseService
{
    /**
     * List plans (resource allocations).
     * Requires: Planner
     */
    public function list(array $params = []): array
    {
        return $this->client->get('plans', $params);
    }

    /**
     * Search plans.
     * Requires: Planner
     */
    public function search(array $criteria): array
    {
        return $this->client->post('plans/search', $criteria);
    }

    /**
     * Get a specific plan.
     * Requires: Planner
     */
    public function get(int $id): ?array
    {
        return $this->client->get("plans/{$id}");
    }

    /**
     * Create a plan.
     * Requires: Planner
     */
    public function create(array $data): array
    {
        return $this->client->post('plans', $data);
    }

    /**
     * Update a plan.
     * Requires: Planner
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put("plans/{$id}", $data);
    }

    /**
     * Delete a plan.
     * Requires: Planner
     */
    public function delete(int $id): bool
    {
        $this->client->delete("plans/{$id}");
        return true;
    }

    /**
     * Get plans for a user.
     * Requires: Planner
     */
    public function byUser(string $accountId): array
    {
        return $this->client->get("plans/user/{$accountId}");
    }
}
