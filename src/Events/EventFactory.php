<?php

namespace NinoHaar\Tempo\Events;

class EventFactory
{
    private static array $eventMap = [
        'worklog.created' => Webhooks\WorklogCreated::class,
        'worklog.updated' => Webhooks\WorklogUpdated::class,
        'worklog.deleted' => Webhooks\WorklogDeleted::class,
        'team.created' => Webhooks\TeamCreated::class,
        'team.updated' => Webhooks\TeamUpdated::class,
        'team.deleted' => Webhooks\TeamDeleted::class,
        'team-membership.created' => Webhooks\TeamMembershipCreated::class,
        'team-membership.updated' => Webhooks\TeamMembershipUpdated::class,
        'team-membership.deleted' => Webhooks\TeamMembershipDeleted::class,
        'account.created' => Webhooks\AccountCreated::class,
        'account.updated' => Webhooks\AccountUpdated::class,
        'account.deleted' => Webhooks\AccountDeleted::class,
        'workload-scheme-membership.created' => Webhooks\WorkloadSchemeMembershipCreated::class,
        'workload-scheme-membership.updated' => Webhooks\WorkloadSchemeMembershipUpdated::class,
        'workload-scheme-membership.deleted' => Webhooks\WorkloadSchemeMembershipDeleted::class,
    ];

    public static function make(string $eventType, array $payload): ?TempoWebhookEvent
    {
        $eventClass = self::$eventMap[$eventType] ?? null;

        if (! $eventClass) {
            return null;
        }

        return new $eventClass($payload);
    }
}
