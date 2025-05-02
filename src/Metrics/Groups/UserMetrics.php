<?php

namespace AnatolyDuzenko\ConfigurablePrometheus\Metrics\Groups;

use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;
use AnatolyDuzenko\ConfigurablePrometheus\DTO\MetricDefinition;
use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;

/**
 * A sample metric group that defines user-related metrics.
 */
class UserMetrics implements MetricGroup
{
    /**
     * Define user metrics like logins and active users.
     *
     * @return array<int, MetricDefinition>
     */
    public function definitions(): array
    {
        return [
            new MetricDefinition(
                name: 'user_logins_total',
                namespace: 'users',
                helpText: 'Total number of user logins.',
                type: MetricType::Counter,
                labelNames: ['app']
            ),
            new MetricDefinition(
                namespace: 'users',
                name: 'active_users',
                helpText: 'Current active user count.',
                type: MetricType::Gauge,
                labelNames: ['app']
            )
        ];
    }
}
