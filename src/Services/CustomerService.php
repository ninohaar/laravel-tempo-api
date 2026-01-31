<?php

namespace NinoHaar\Tempo\Services;

class CustomerService extends BaseService
{
    public function list(array $params = []): array
    {
        return $this->client->get('customers', $params);
    }

    public function search(array $criteria): array
    {
        return $this->client->post('customers/search', $criteria);
    }

    public function get(int $id): ?array
    {
        return $this->client->get("customers/{$id}");
    }

    public function create(array $data): array
    {
        return $this->client->post('customers', $data);
    }

    public function update(int $id, array $data): array
    {
        return $this->client->put("customers/{$id}", $data);
    }

    public function delete(int $id): bool
    {
        $this->client->delete("customers/{$id}");
        return true;
    }

    public function accounts(int $id): array
    {
        return $this->client->get("customers/{$id}/accounts");
    }
}
