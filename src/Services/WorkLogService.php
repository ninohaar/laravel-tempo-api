<?php

namespace NinoHaar\Tempo\Services;

use NinoHaar\Tempo\Models\WorkLog;

class WorkLogService extends BaseService
{
    /**
     * List worklogs with optional filtering.
     *
     * @param  array{from?: string, to?: string, offset?: int, limit?: int, project?: int|array, user?: string, issue?: string, account?: string}  $params
     * @return array{results: array<WorkLog>, metadata: array}
     */
    public function list(array $params = []): array
    {
        $query = array_filter([
            'from' => $params['from'] ?? null,
            'to' => $params['to'] ?? null,
            'offset' => $params['offset'] ?? 0,
            'limit' => min($params['limit'] ?? 50, 1000),
        ]);

        return $this->client->get('worklogs', $query);
    }

    /**
     * Get a specific worklog.
     */
    public function get(int $id): ?WorkLog
    {
        $data = $this->client->get("worklogs/{$id}");
        return ! empty($data) ? new WorkLog($data) : null;
    }

    /**
     * Create a new worklog.
     *
     * @param  array{issueId: string, timeSpentSeconds: int, startDate: string, startTime: string, description?: string, authorAccountId?: string, attributes?: array}  $data
     */
    public function create(array $data): WorkLog
    {
        $response = $this->client->post('worklogs', $data);
        return new WorkLog($response);
    }

    /**
     * Update a worklog.
     */
    public function update(int $id, array $data): WorkLog
    {
        $response = $this->client->put("worklogs/{$id}", $data);
        return new WorkLog($response);
    }

    /**
     * Delete a worklog.
     */
    public function delete(int $id): bool
    {
        $this->client->delete("worklogs/{$id}");
        return true;
    }

    /**
     * Search worklogs using advanced criteria.
     *
     * @param  array  $criteria
     * @return array
     */
    public function search(array $criteria): array
    {
        return $this->client->post('worklogs/search', $criteria);
    }

    /**
     * Bulk create worklogs.
     *
     * @param  array<array>  $worklogs
     * @return array
     */
    public function bulkCreate(array $worklogs): array
    {
        return $this->client->post('worklogs/bulk', ['worklogs' => $worklogs]);
    }

    /**
     * Get worklogs for a specific account.
     */
    public function byAccount(string $accountKey, array $params = []): array
    {
        return $this->client->get("worklogs/account/{$accountKey}", $params);
    }

    /**
     * Get worklogs for a specific Jira issue.
     */
    public function byIssue(string $issueId, array $params = []): array
    {
        return $this->client->get("worklogs/issue/{$issueId}", $params);
    }

    /**
     * Get worklogs for a specific project.
     */
    public function byProject(int $projectId, array $params = []): array
    {
        return $this->client->get("worklogs/project/{$projectId}", $params);
    }

    /**
     * Get worklogs for a specific team.
     */
    public function byTeam(int $teamId, array $params = []): array
    {
        return $this->client->get("worklogs/team/{$teamId}", $params);
    }

    /**
     * Get worklogs for a specific user.
     */
    public function byUser(string $accountId, array $params = []): array
    {
        return $this->client->get("worklogs/user/{$accountId}", $params);
    }

    /**
     * Convert Jira worklog IDs to Tempo worklog IDs.
     *
     * @param  array<string>  $issueIds
     * @return array
     */
    public function jiraToTempo(array $issueIds): array
    {
        return $this->client->post('worklogs/jira-to-tempo', ['issues' => $issueIds]);
    }

    /**
     * Convert Tempo worklog IDs to Jira worklog IDs.
     *
     * @param  array<int>  $worklogIds
     * @return array
     */
    public function tempoToJira(array $worklogIds): array
    {
        return $this->client->post('worklogs/tempo-to-jira', ['tempoWorklogIds' => $worklogIds]);
    }
}
