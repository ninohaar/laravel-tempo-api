<?php

namespace NinoHaar\Tempo\Services;

class FinancialService extends BaseService
{
    /**
     * Get project budget.
     * Requires: Financial Manager
     */
    public function budget(int $projectId): ?array
    {
        return $this->client->get("projects/{$projectId}/budget");
    }

    /**
     * Set project budget.
     * Requires: Financial Manager
     */
    public function setBudget(int $projectId, array $data): array
    {
        return $this->client->post("projects/{$projectId}/budget", $data);
    }

    /**
     * Get budget milestones.
     * Requires: Financial Manager
     */
    public function milestones(int $projectId): array
    {
        return $this->client->get("projects/{$projectId}/budget-milestones");
    }

    /**
     * Get project expenses.
     * Requires: Financial Manager
     */
    public function expenses(int $projectId, array $params = []): array
    {
        return $this->client->get("projects/{$projectId}/expenses", $params);
    }

    /**
     * Create expense.
     * Requires: Financial Manager
     */
    public function createExpense(int $projectId, array $data): array
    {
        return $this->client->post("projects/{$projectId}/expenses", $data);
    }

    /**
     * Get fixed revenues.
     * Requires: Financial Manager
     */
    public function fixedRevenues(int $projectId): array
    {
        return $this->client->get("projects/{$projectId}/fixed-revenues");
    }

    /**
     * Create fixed revenue.
     * Requires: Financial Manager
     */
    public function createFixedRevenue(int $projectId, array $data): array
    {
        return $this->client->post("projects/{$projectId}/fixed-revenues", $data);
    }

    /**
     * Get labor actuals for project.
     * Requires: Financial Manager
     */
    public function laborActuals(int $projectId): array
    {
        return $this->client->get("projects/{$projectId}/actuals/labor");
    }

    /**
     * Get expense actuals for project.
     * Requires: Financial Manager
     */
    public function expenseActuals(int $projectId): array
    {
        return $this->client->get("projects/{$projectId}/actuals/expenses");
    }

    /**
     * Get financial summary.
     * Requires: Financial Manager
     */
    public function summary(int $projectId): array
    {
        return $this->client->get("projects/{$projectId}/financials/summary");
    }

    /**
     * Get portfolios.
     * Requires: Financial Manager
     */
    public function portfolios(array $params = []): array
    {
        return $this->client->get('portfolios', $params);
    }

    /**
     * Create portfolio.
     * Requires: Financial Manager
     */
    public function createPortfolio(array $data): array
    {
        return $this->client->post('portfolios', $data);
    }

    /**
     * Get billing rates tables.
     * Requires: Financial Manager
     */
    public function billingRatesTables(array $params = []): array
    {
        return $this->client->get('billing-rates-tables', $params);
    }

    /**
     * Get global rates by role.
     * Requires: Financial Manager
     */
    public function globalRates(): array
    {
        return $this->client->get('global-rates/by-role');
    }
}
