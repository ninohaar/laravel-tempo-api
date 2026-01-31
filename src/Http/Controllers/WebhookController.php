<?php

namespace NinoHaar\Tempo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use NinoHaar\Tempo\Events\EventFactory;
use NinoHaar\Tempo\Exceptions\WebhookValidationException;

class WebhookController extends Controller
{
    /**
     * Handle incoming Tempo webhook.
     * Route: POST /api/webhooks/tempo (configurable)
     */
    public function handle(Request $request): Response
    {
        // Validate webhook signature
        $this->validateSignature($request);

        $payload = $request->json()->all();

        // Get event type from payload
        $eventType = $payload['resourceType'] ?? null;

        if (! $eventType) {
            return response('', 400);
        }

        // Create and dispatch event
        $event = EventFactory::make($eventType, $payload);

        if ($event) {
            event($event);
        }

        return response('', 200);
    }

    /**
     * Validate webhook signature using HMAC-SHA256.
     *
     * @throws WebhookValidationException
     */
    private function validateSignature(Request $request): void
    {
        $signature = $request->header('X-Tempo-Signature');

        if (! $signature) {
            throw WebhookValidationException::missingSignature();
        }

        $secret = config('tempo.webhooks.signature_secret');

        if (! $secret) {
            throw WebhookValidationException::secretNotConfigured();
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw WebhookValidationException::invalidSignature();
        }
    }
}
