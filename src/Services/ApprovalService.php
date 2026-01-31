<?php

namespace NinoHaar\Tempo\Services;

class ApprovalService extends BaseService
{
    /**
     * Get timesheet approvals for a user.
     * Requires: Timesheets
     */
    public function userTimesheet(string $accountId): ?array
    {
        return $this->client->get("timesheet-approvals/user/{$accountId}");
    }

    /**
     * Approve user timesheet.
     * Requires: Timesheets
     */
    public function approveTimesheet(string $accountId, array $data = []): array
    {
        return $this->client->post("timesheet-approvals/user/{$accountId}/approve", $data);
    }

    /**
     * Reject user timesheet.
     * Requires: Timesheets
     */
    public function rejectTimesheet(string $accountId, array $data = []): array
    {
        return $this->client->post("timesheet-approvals/user/{$accountId}/reject", $data);
    }

    /**
     * Submit user timesheet.
     * Requires: Timesheets
     */
    public function submitTimesheet(string $accountId): array
    {
        return $this->client->post("timesheet-approvals/user/{$accountId}/submit");
    }

    /**
     * Get project time approvals.
     * Requires: Timesheets
     */
    public function projectTime(int $projectId): array
    {
        return $this->client->get("projects/{$projectId}/time-approvals");
    }

    /**
     * Approve project time.
     * Requires: Timesheets
     */
    public function approveProjectTime(int $projectId, array $data): array
    {
        return $this->client->post("projects/{$projectId}/time-approvals/approve", $data);
    }

    /**
     * Get plan approvals for review.
     * Requires: Planner
     */
    public function plansForReview(array $params = []): array
    {
        return $this->client->post('plan-approvals/plans-for-review', $params);
    }

    /**
     * Update plan approval.
     * Requires: Planner
     */
    public function updatePlanApproval(int $id, array $data): array
    {
        return $this->client->put("plan-approvals/{$id}", $data);
    }
}
