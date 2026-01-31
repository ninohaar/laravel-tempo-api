# Laravel Tempo API Client

A comprehensive Laravel package for integrating with the Tempo Cloud REST API v4. Supports all Tempo API endpoints with automatic OAuth2 token refresh, optional caching, webhook handling, and Laravel event integration.

## Features

- ✅ **Complete API Coverage**: All 200+ Tempo API v4 endpoints across 15+ service categories
- ✅ **Authentication**: Bearer Token and OAuth2 support with automatic token refresh
- ✅ **Lazy-Loaded Services**: Services load only when requested, minimal memory footprint
- ✅ **Caching**: Optional built-in caching with token-aware cache keys for multi-user safety
- ✅ **Webhook Integration**: Full webhook support with signature validation and Laravel event dispatching
- ✅ **Error Handling**: Comprehensive exception handling with retry logic and rate limit detection
- ✅ **Artisan Commands**: Convenient commands for OAuth2 token refresh and webhook secret rotation

## Requirements

- PHP >= 8.2
- Laravel >= 11.0 or >= 12.0
- Guzzle HTTP client
- OAuth2 client library

## Installation

### 1. Add VCS Repository

Add the package repository to your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@git.indicia.nl:nino.haar/laravel-tempo-api.git"
        }
    ]
}
```

### 2. Install Package

```bash
composer require ninohaar/laravel-tempo-api
```

### 3. Publish Configuration (Optional)

Optionally publish the configuration file to customize settings:

```bash
php artisan vendor:publish --provider="NinoHaar\Tempo\TempoServiceProvider" --tag=tempo-config
```

This creates `config/tempo.php` in your project. The package works with default configuration loaded from `.env`.

### 4. Configure Environment Variables

Add the following to your `.env` file:

```bash
# Tempo API Configuration
TEMPO_BASE_URL=https://api.tempo.io
TEMPO_REGION=global
TEMPO_API_VERSION=4

# Bearer Token Authentication (choose one)
TEMPO_AUTH_TYPE=token
TEMPO_TOKEN=your_tempo_bearer_token_here

# OR OAuth2 Authentication
TEMPO_AUTH_TYPE=oauth2
TEMPO_CLIENT_ID=your_client_id
TEMPO_CLIENT_SECRET=your_client_secret

# Webhook Configuration
TEMPO_WEBHOOK_SECRET=your_webhook_secret

# Optional: Caching
TEMPO_CACHE_ENABLED=false
TEMPO_CACHE_TTL=3600
```

## Configuration

### Bearer Token (Simple)

1. Go to Tempo > Settings > Data Access > API integration
2. Generate or copy your API token
3. Add to `.env`:

```bash
TEMPO_AUTH_TYPE=token
TEMPO_TOKEN=your_token_here
```

### OAuth2 Authentication (Application)

1. Go to Tempo > Settings > Data Access > OAuth 2.0 authentication
2. Create OAuth credentials (Client ID, Client Secret)
3. Add to `.env`:

```bash
TEMPO_AUTH_TYPE=oauth2
TEMPO_CLIENT_ID=your_client_id
TEMPO_CLIENT_SECRET=your_client_secret
```

The package automatically:
- Requests access tokens on first use
- Refreshes tokens when they expire
- Stores tokens in `.env` file for persistence
- Handles refresh failures gracefully (disables auto-refresh, requires manual command)

### Optional Caching

Enable optional built-in caching in `.env`:

```bash
TEMPO_CACHE_ENABLED=true
TEMPO_CACHE_TTL=3600
```

Or customize per-endpoint TTL in `config/tempo.php`:

```php
'cache' => [
    'enabled' => true,
    'endpoints' => [
        'teams' => 3600,
        'roles' => 3600,
        'projects' => 1800,
    ],
],
```

**Security Note**: Cache keys include token information to prevent data collision between users. Ensure your cache backend (Redis, Memcached) is properly secured in shared environments.

## Usage

### Using the Facade

```php
use NinoHaar\Tempo\Facades\Tempo;

// Worklogs
$worklogs = Tempo::worklogs()->list(['from' => '2024-01-01', 'to' => '2024-01-31']);
$worklog = Tempo::worklogs()->get(123);
$created = Tempo::worklogs()->create([
    'issueId' => 'JIRA-123',
    'timeSpentSeconds' => 3600,
    'startDate' => '2024-01-15',
    'startTime' => '09:00',
]);

// Teams
$teams = Tempo::teams()->list();
$team = Tempo::teams()->get(456);
$members = Tempo::teams()->members(456);

// Projects
$projects = Tempo::projects()->list();
$project = Tempo::projects()->get(789);
$scope = Tempo::projects()->scope(789);

// Approvals
$timesheet = Tempo::approvals()->userTimesheet('account-id');
Tempo::approvals()->approveTimesheet('account-id');

// And more...
```

### Dependency Injection

```php
use NinoHaar\Tempo\Services\WorkLogService;

class MyController extends Controller
{
    public function __construct(private WorkLogService $worklogService) {}

    public function index()
    {
        $worklogs = $this->worklogService->list();
        return response()->json($worklogs);
    }
}
```

### Manual HTTP Requests

```php
use NinoHaar\Tempo\Client\TempoClient;

$client = app(TempoClient::class);

// GET request
$data = $client->get('worklogs', ['from' => '2024-01-01']);

// POST request
$response = $client->post('worklogs', [
    'issueId' => 'JIRA-123',
    'timeSpentSeconds' => 3600,
]);

// PUT request
$updated = $client->put('worklogs/123', ['description' => 'Updated']);

// DELETE request
$client->delete('worklogs/123');
```

## Webhooks

### 1. Subscribe to Webhooks

```php
use NinoHaar\Tempo\Facades\Tempo;

// Create webhook subscription
Tempo::webhooks()->createSubscription([
    'events' => ['worklog.created', 'worklog.updated'],
    'url' => 'https://yourapp.com/api/webhooks/tempo',
]);
```

The webhook controller is **automatically registered** at:
- **Default**: `/api/webhooks/tempo`
- **Configurable**: Set `TEMPO_WEBHOOK_ROUTE` in `.env`
- **Named Route**: `tempo.webhook`

### 2. Handle Events

Create a listener:

```php
php artisan make:listener HandleWorklogCreated --event="NinoHaar\\Tempo\\Events\\Webhooks\\WorklogCreated"
```

Implement listener:

```php
namespace App\Listeners;

use NinoHaar\Tempo\Events\Webhooks\WorklogCreated;

class HandleWorklogCreated
{
    public function handle(WorklogCreated $event): void
    {
        $payload = $event->payload;
        
        // Process worklog creation
        Log::info('Worklog created:', $payload);
    }
}
```

Register in `EventServiceProvider`:

```php
protected $listen = [
    WorklogCreated::class => [
        HandleWorklogCreated::class,
    ],
];
```

### Supported Webhook Events

- `worklog.created`, `worklog.updated`, `worklog.deleted`
- `team.created`, `team.updated`, `team.deleted`
- `team-membership.created`, `team-membership.updated`, `team-membership.deleted`
- `account.created`, `account.updated`, `account.deleted`
- `workload-scheme-membership.created`, `workload-scheme-membership.updated`, `workload-scheme-membership.deleted`

## Artisan Commands

### Refresh OAuth2 Token

Manually refresh the OAuth2 access token (required if refresh fails):

```bash
php artisan tempo:oauth-refresh
```

This command:
1. Requests a new access token
2. Updates `.env` with the new token
3. Resets the `TEMPO_OAUTH_REFRESH_DISABLED` flag

### Rotate Webhook Secret

Rotate your webhook secret:

```bash
php artisan tempo:rotate-webhook-secret

# Or provide custom secret
php artisan tempo:rotate-webhook-secret "your-secret-here"
```

## Service Classes

### WorkLogService
Manage time tracking worklogs, work attributes, and ID conversions.

```php
Tempo::worklogs()->list($params);
Tempo::worklogs()->get($id);
Tempo::worklogs()->create($data);
Tempo::worklogs()->byProject($projectId);
Tempo::worklogs()->bulkCreate($worklogs);
```

### TeamService
Manage teams, memberships, and links.

```php
Tempo::teams()->list();
Tempo::teams()->get($id);
Tempo::teams()->members($teamId);
```

### ProjectService
Manage projects, scope, timeframe, budget, expenses. **Requires: Financial Manager**

```php
Tempo::projects()->list();
Tempo::projects()->scope($id);
Tempo::projects()->budget($id);
Tempo::projects()->expenses($id);
```

### ApprovalService
Manage timesheet, project time, and plan approvals. **Requires: Timesheets for time-approvals**

```php
Tempo::approvals()->userTimesheet($accountId);
Tempo::approvals()->approveTimesheet($accountId);
Tempo::approvals()->projectTime($projectId); // Requires: Timesheets
Tempo::approvals()->plansForReview(); // Requires: Planner
```

### PlanService
Manage plans and allocations. **Requires: Planner**

```php
Tempo::plans()->list();
Tempo::plans()->search($criteria);
Tempo::plans()->create($data);
```

### FinancialService
Manage financial data (budget, expenses, revenue, portfolios). **Requires: Financial Manager**

```php
Tempo::financial()->budget($projectId);
Tempo::financial()->expenses($projectId);
Tempo::financial()->portfolios();
Tempo::financial()->globalRates();
```

### ConfigurationService
Manage global configuration, periods, schemes, schedules.

```php
Tempo::configuration()->globalConfiguration();
Tempo::configuration()->periods($params);
Tempo::configuration()->holidaySchemes();
Tempo::configuration()->workloadSchemes();
Tempo::configuration()->userSchedule($accountId);
```

### AccountService
Manage accounts and categories.

```php
Tempo::accounts()->list();
Tempo::accounts()->get($key);
Tempo::accounts()->categories();
```

### Other Services
- `RoleService` - Manage roles
- `CustomerService` - Manage customers
- `WebhookService` - Manage webhook subscriptions
- `AuditService` - View audit events
- `ProgramService` - Manage programs
- `PermissionRoleService` - Manage permission roles
- `SkillService` - Manage skills (Planner)

## Pagination

All list endpoints support pagination with `offset` and `limit` parameters:

```php
$worklogs = Tempo::worklogs()->list([
    'from' => '2024-01-01',
    'to' => '2024-01-31',
    'offset' => 0,
    'limit' => 50,
]);

// Response includes metadata:
// {
//     "results": [...],
//     "metadata": {
//         "count": 100,
//         "offset": 0,
//         "limit": 50,
//         "next": "/worklogs?offset=50&limit=50",
//         "previous": null
//     }
// }
```

## Error Handling

The package includes comprehensive exception handling:

```php
use NinoHaar\Tempo\Exceptions\{
    TempoException,
    AuthenticationException,
    RateLimitException,
    NotFoundException,
    ValidationException,
    WebhookValidationException,
    TokenRefreshFailedException,
};

try {
    $worklog = Tempo::worklogs()->get(999);
} catch (NotFoundException $e) {
    // Handle not found
} catch (RateLimitException $e) {
    // Handle rate limit, wait $e->getRetryAfter() seconds
} catch (AuthenticationException $e) {
    // Handle authentication errors
} catch (TempoException $e) {
    // Handle other API errors
}
```

## Product Requirements

Some endpoints require specific Tempo products:

| Product | Service Methods | Status |
|---------|-----------------|--------|
| **Financial Manager** | `ProjectService::*`, `FinancialService::*` | Optional |
| **Planner** | `PlanService::*`, `SkillService::*`, `ApprovalService::plansForReview()` | Optional |
| **Timesheets** | `ApprovalService::*` (except plans), Project time approvals | Optional |

## Testing

Run tests:

```bash
./vendor/bin/phpunit
```

## Security Warnings

⚠️ **OAuth2 Token Storage**: Tokens are stored in `.env` file for simple deployment. This is suitable for private servers and development environments.

For production shared server environments, consider:
1. Using encrypted .env configuration
2. Database token storage with encryption
3. Vault/secrets management services
4. Restrict file permissions on .env

⚠️ **Webhook Signature Validation**: Always validate webhook signatures. The package validates HMAC-SHA256 signatures automatically.

⚠️ **Cache Security**: Cache keys include token information. Ensure cache backends (Redis, Memcached) are properly secured in shared environments.

## License

MIT License - see LICENSE file for details

## Support

For issues, questions, or contributions, contact the maintainer or create issues in the repository.
