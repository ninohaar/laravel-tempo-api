<?php

namespace NinoHaar\Tempo;

use Illuminate\Container\Container;
use NinoHaar\Tempo\Services\AccountService;
use NinoHaar\Tempo\Services\ApprovalService;
use NinoHaar\Tempo\Services\AuditService;
use NinoHaar\Tempo\Services\ConfigurationService;
use NinoHaar\Tempo\Services\CustomerService;
use NinoHaar\Tempo\Services\FinancialService;
use NinoHaar\Tempo\Services\PermissionRoleService;
use NinoHaar\Tempo\Services\PlanService;
use NinoHaar\Tempo\Services\ProgramService;
use NinoHaar\Tempo\Services\ProjectService;
use NinoHaar\Tempo\Services\RoleService;
use NinoHaar\Tempo\Services\SkillService;
use NinoHaar\Tempo\Services\TeamService;
use NinoHaar\Tempo\Services\WebhookService;
use NinoHaar\Tempo\Services\WorkLogService;

/**
 * Tempo API Manager
 *
 * Provides convenient access to all Tempo API services.
 */
class TempoManager
{
    public function __construct(private Container $container)
    {
    }

    public function worklogs(): WorkLogService
    {
        return $this->container->make(WorkLogService::class);
    }

    public function teams(): TeamService
    {
        return $this->container->make(TeamService::class);
    }

    public function roles(): RoleService
    {
        return $this->container->make(RoleService::class);
    }

    public function accounts(): AccountService
    {
        return $this->container->make(AccountService::class);
    }

    public function customers(): CustomerService
    {
        return $this->container->make(CustomerService::class);
    }

    public function projects(): ProjectService
    {
        return $this->container->make(ProjectService::class);
    }

    public function approvals(): ApprovalService
    {
        return $this->container->make(ApprovalService::class);
    }

    public function plans(): PlanService
    {
        return $this->container->make(PlanService::class);
    }

    public function financial(): FinancialService
    {
        return $this->container->make(FinancialService::class);
    }

    public function configuration(): ConfigurationService
    {
        return $this->container->make(ConfigurationService::class);
    }

    public function audit(): AuditService
    {
        return $this->container->make(AuditService::class);
    }

    public function webhooks(): WebhookService
    {
        return $this->container->make(WebhookService::class);
    }

    public function programs(): ProgramService
    {
        return $this->container->make(ProgramService::class);
    }

    public function permissionRoles(): PermissionRoleService
    {
        return $this->container->make(PermissionRoleService::class);
    }

    public function skills(): SkillService
    {
        return $this->container->make(SkillService::class);
    }
}
