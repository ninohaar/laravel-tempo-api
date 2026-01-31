<?php

namespace NinoHaar\Tempo\Console\Commands;

use Illuminate\Console\Command;
use NinoHaar\Tempo\Client\EnvFileUpdater;
use NinoHaar\Tempo\Client\Authenticators\OAuth2Authenticator;
use NinoHaar\Tempo\Configuration\DotEnvConfiguration;

class RefreshOAuth2Token extends Command
{
    protected $signature = 'tempo:oauth-refresh';

    protected $description = 'Manually refresh Tempo OAuth2 access token and update .env';

    public function handle(): int
    {
        $config = new DotEnvConfiguration();

        if ($config->getAuthType() !== 'oauth2') {
            $this->error('OAuth2 authentication is not enabled.');
            return 1;
        }

        try {
            $this->info('Refreshing OAuth2 token...');

            $envUpdater = new EnvFileUpdater();
            $authenticator = new OAuth2Authenticator($config, $envUpdater);
            $authenticator->refreshAccessToken();

            $this->info('✓ OAuth2 token refreshed and .env updated successfully.');
            $this->info('TEMPO_OAUTH_REFRESH_DISABLED has been set to false.');

            // Reset refresh disabled flag
            $envUpdater->update([
                'TEMPO_OAUTH_REFRESH_DISABLED' => 'false',
            ]);

            return 0;
        } catch (\Throwable $e) {
            $this->error('Failed to refresh token: '.$e->getMessage());
            return 1;
        }
    }
}
