<?php

namespace NinoHaar\Tempo\Services;

class WebhookService extends BaseService
{
    public function subscriptions(array $params = []): array
    {
        return $this->client->get('webhooks/subscriptions', $params);
    }

    public function createSubscription(array $data): array
    {
        return $this->client->post('webhooks/subscriptions', $data);
    }

    public function getSubscription(int $id): ?array
    {
        return $this->client->get("webhooks/subscriptions/{$id}");
    }

    public function deleteSubscription(int $id): bool
    {
        $this->client->delete("webhooks/subscriptions/{$id}");
        return true;
    }

    public function refreshSubscription(int $id): array
    {
        return $this->client->post("webhooks/subscriptions/{$id}/refresh");
    }
}
