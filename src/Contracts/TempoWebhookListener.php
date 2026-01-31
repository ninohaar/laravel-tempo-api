<?php

namespace NinoHaar\Tempo\Contracts;

interface TempoWebhookListener
{
    /**
     * Handle a Tempo webhook event.
     *
     * @param  array  $payload
     */
    public function handle(array $payload): void;
}
