<?php

namespace NinoHaar\Tempo\Services;

class AuditService extends BaseService
{
    public function search(array $criteria): array
    {
        return $this->client->post('audit/events/search', $criteria);
    }

    public function deletedWorklogs(): array
    {
        return $this->client->get('papertrail/1/events/deleted/types/worklog');
    }

    public function deletedAllocations(): array
    {
        return $this->client->get('papertrail/1/events/deleted/types/allocation');
    }
}
