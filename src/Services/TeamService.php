<?php

namespace NinoHaar\Tempo\Services;

class TeamService extends BaseService
{
    /**
     * List teams.
     *
     * @param  array{offset?: int, limit?: int}  $params
     */
    public function list(array $params = []): array
    {
        $query = array_filter([
            'offset' => $params['offset'] ?? 0,
            'limit' => min($params['limit'] ?? 50, 1000),
        ]);

        return $this->cache->remember(
            $this->cacheKey('teams', $query),
            fn () => $this->client->get('teams', $query),
            3600
        );
    }

    /**
     * Get a specific team.
     */
    public function get(int $id): ?array
    {
        return $this->cache->remember(
            $this->cacheKey('team', ['id' => $id]),
            fn () => $this->client->get("teams/{$id}"),
            3600
        );
    }

    /**
     * Create a new team.
     */
    public function create(array $data): array
    {
        $result = $this->client->post('teams', $data);
        $this->cache->forget($this->cacheKey('teams'));
        return $result;
    }

    /**
     * Update a team.
     */
    public function update(int $id, array $data): array
    {
        $result = $this->client->put("teams/{$id}", $data);
        $this->cache->forget($this->cacheKey('team', ['id' => $id]));
        $this->cache->forget($this->cacheKey('teams'));
        return $result;
    }

    /**
     * Delete a team.
     */
    public function delete(int $id): bool
    {
        $this->client->delete("teams/{$id}");
        $this->cache->forget($this->cacheKey('team', ['id' => $id]));
        $this->cache->forget($this->cacheKey('teams'));
        return true;
    }

    /**
     * Get team members.
     */
    public function members(int $teamId): array
    {
        return $this->cache->remember(
            $this->cacheKey('team_members', ['team_id' => $teamId]),
            fn () => $this->client->get("teams/{$teamId}/members"),
            3600
        );
    }

    /**
     * Get team links.
     */
    public function links(int $teamId): array
    {
        return $this->client->get("teams/{$teamId}/links");
    }
}
