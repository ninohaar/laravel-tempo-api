<?php

namespace NinoHaar\Tempo\Events;

use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class TempoWebhookEvent
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public function __construct(
        public array $payload,
    ) {
    }
}
