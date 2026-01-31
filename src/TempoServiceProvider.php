<?php

namespace NinoHaar\Tempo;

use Illuminate\Support\ServiceProvider;
use NinoHaar\Tempo\Client\CacheDecorator;
use NinoHaar\Tempo\Client\EnvFileUpdater;
use NinoHaar\Tempo\Client\TempoClient;
use NinoHaar\Tempo\Configuration\ConfigurationInterface;
use NinoHaar\Tempo\Configuration\DotEnvConfiguration;
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

class TempoServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container (deferred).
     * Services are only loaded when explicitly requested.
     */
    public bool $defer = true;

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tempo.php', 'tempo');

        // Core services
        $this->app->singleton(ConfigurationInterface::class, DotEnvConfiguration::class);
        $this->app->singleton(EnvFileUpdater::class);

        $this->app->singleton(TempoClient::class, function ($app) {
            return new TempoClient(
                $app->make(ConfigurationInterface::class),
                $app->make(EnvFileUpdater::class),
            );
        });

        $this->app->singleton(CacheDecorator::class, function ($app) {
            return new CacheDecorator(
                $app->make(TempoClient::class),
                $app->make(ConfigurationInterface::class),
            );
        });

        // Lazy-load service classes
        $this->registerServices();

        // Register facade
        $this->registerFacade();

        // Register commands
        $this->commands([
            Console\Commands\RefreshOAuth2Token::class,
            Console\Commands\RotateWebhookSecret::class,
        ]);
    }

    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/tempo.php' => config_path('tempo.php'),
        ], 'tempo-config');

        // Register webhook route
        $this->registerWebhookRoute();
    }

    private function registerServices(): void
    {
        $services = [
            'tempo.worklog' => WorkLogService::class,
            'tempo.team' => TeamService::class,
            'tempo.role' => RoleService::class,
            'tempo.account' => AccountService::class,
            'tempo.customer' => CustomerService::class,
            'tempo.project' => ProjectService::class,
            'tempo.approval' => ApprovalService::class,
            'tempo.plan' => PlanService::class,
            'tempo.financial' => FinancialService::class,
            'tempo.configuration' => ConfigurationService::class,
            'tempo.audit' => AuditService::class,
            'tempo.webhook' => WebhookService::class,
            'tempo.program' => ProgramService::class,
            'tempo.permission_role' => PermissionRoleService::class,
            'tempo.skill' => SkillService::class,
        ];

        foreach ($services as $key => $service) {
            $this->app->singleton($service, function ($app) use ($service) {
                return new $service(
                    $app->make(CacheDecorator::class),
                    $app->make(TempoClient::class),
                );
            });
        }
    }

    private function registerFacade(): void
    {
        $this->app->singleton('tempo', function ($app) {
            return new TempoManager($app);
        });
    }

    private function registerWebhookRoute(): void
    {
        if (! function_exists('route')) {
            return;
        }

        $routePath = config('tempo.webhooks.route_path', '/api/webhooks/tempo');
        $routeName = config('tempo.webhooks.route_name', 'tempo.webhook');

        \Illuminate\Support\Facades\Route::post($routePath, [
            Http\Controllers\WebhookController::class,
            'handle',
        ])->name($routeName);
    }

    /**
     * Get the services provided by the provider.
     * This enables deferred loading.
     */
    public function provides(): array
    {
        return [
            TempoClient::class,
            CacheDecorator::class,
            ConfigurationInterface::class,
            EnvFileUpdater::class,
            'tempo',
            'tempo.worklog',
            'tempo.team',
            'tempo.role',
            'tempo.account',
            'tempo.customer',
            'tempo.project',
            'tempo.approval',
            'tempo.plan',
            'tempo.financial',
            'tempo.configuration',
            'tempo.audit',
            'tempo.webhook',
            'tempo.program',
            'tempo.permission_role',
            'tempo.skill',
        ];
    }
}
