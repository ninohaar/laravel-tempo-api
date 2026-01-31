<?php

namespace NinoHaar\Tempo\Services;

class SkillService extends BaseService
{
    /**
     * List skills.
     * Requires: Planner
     */
    public function list(array $params = []): array
    {
        return $this->client->get('skills', $params);
    }

    /**
     * Create skill.
     * Requires: Planner
     */
    public function create(array $data): array
    {
        return $this->client->post('skills', $data);
    }

    /**
     * Get skill.
     * Requires: Planner
     */
    public function get(int $id): ?array
    {
        return $this->client->get("skills/{$id}");
    }

    /**
     * Update skill.
     * Requires: Planner
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put("skills/{$id}", $data);
    }

    /**
     * Delete skill.
     * Requires: Planner
     */
    public function delete(int $id): bool
    {
        $this->client->delete("skills/{$id}");
        return true;
    }
}
