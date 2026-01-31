<?php

namespace NinoHaar\Tempo\Console\Commands;

use Illuminate\Console\Command;
use NinoHaar\Tempo\Client\EnvFileUpdater;
use NinoHaar\Tempo\Client\TempoClient;
use NinoHaar\Tempo\Configuration\DotEnvConfiguration;
use Symfony\Component\Console\Input\InputArgument;

class RotateWebhookSecret extends Command
{
    protected $signature = 'tempo:rotate-webhook-secret {secret?}';

    protected $description = 'Rotate Tempo webhook secret and update .env';

    public function handle(): int
    {
        $config = new DotEnvConfiguration();
        $envUpdater = new EnvFileUpdater();

        try {
            $secret = $this->argument('secret') ?? bin2hex(random_bytes(32));

            $this->info('Rotating webhook secret...');

            // Update .env with new secret
            $envUpdater->update([
                'TEMPO_WEBHOOK_SECRET' => $secret,
            ]);

            $this->info('✓ Webhook secret rotated successfully.');
            $this->info('New secret: '.$secret);
            $this->info('.env file updated.');

            return 0;
        } catch (\Throwable $e) {
            $this->error('Failed to rotate webhook secret: '.$e->getMessage());
            return 1;
        }
    }
}
