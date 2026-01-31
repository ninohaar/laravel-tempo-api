<?php

namespace NinoHaar\Tempo\Models;

class WorkLog
{
    public int $id;

    public string $issueId;

    public int $timeSpentSeconds;

    public string $startDate;

    public string $startTime;

    public ?string $description = null;

    public ?string $authorAccountId = null;

    public array $attributes = [];

    public ?int $updateAuthorAccountId = null;

    public ?string $created = null;

    public ?string $updated = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
