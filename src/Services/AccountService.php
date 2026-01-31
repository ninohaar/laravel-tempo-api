<?php

namespace NinoHaar\Tempo\Services;

class AccountService extends BaseService
{
    public function list(array $params = []): array
    {
        return $this->client->get('accounts', $params);
    }

    public function search(array $criteria): array
    {
        return $this->client->post('accounts/search', $criteria);
    }

    public function get(string $key): ?array
    {
        return $this->client->get("accounts/{$key}");
    }

    public function create(array $data): array
    {
        return $this->client->post('accounts', $data);
    }

    public function update(string $key, array $data): array
    {
        return $this->client->put("accounts/{$key}", $data);
    }

    public function delete(string $key): bool
    {
        $this->client->delete("accounts/{$key}");
        return true;
    }

    public function links(string $key): array
    {
        return $this->client->get("accounts/{$key}/links");
    }

    public function categories(): array
    {
        return $this->client->get('account-categories');
    }

    public function createCategory(array $data): array
    {
        return $this->client->post('account-categories', $data);
    }
}
