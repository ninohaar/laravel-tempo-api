<?php

namespace NinoHaar\Tempo\Services;

class ProgramService extends BaseService
{
    public function list(array $params = []): array
    {
        return $this->client->get('programs', $params);
    }

    public function teams(int $id): array
    {
        return $this->client->get("programs/{$id}/teams");
    }
}
