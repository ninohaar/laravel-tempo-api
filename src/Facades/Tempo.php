<?php

namespace NinoHaar\Tempo\Facades;

use Illuminate\Support\Facades\Facade;
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
 * @method static WorkLogService worklogs()
 * @method static TeamService teams()
 * @method static RoleService roles()
 * @method static AccountService accounts()
 * @method static CustomerService customers()
 * @method static ProjectService projects()
 * @method static ApprovalService approvals()
 * @method static PlanService plans()
 * @method static FinancialService financial()
 * @method static ConfigurationService configuration()
 * @method static AuditService audit()
 * @method static WebhookService webhooks()
 * @method static ProgramService programs()
 * @method static PermissionRoleService permissionRoles()
 * @method static SkillService skills()
 *
 * @see \NinoHaar\Tempo\TempoManager
 */
class Tempo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tempo';
    }
}
