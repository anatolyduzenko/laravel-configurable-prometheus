<?php 

namespace AnatolyDuzenko\ConfigurablePrometheus\Contracts;

use AnatolyDuzenko\ConfigurablePrometheus\DTO\MetricDefinition;

/**
 * Interface MetricGroup
 *
 * Represents a group of related Prometheus metrics.
 * 
 * @return MetricDefinition[] 
 */
interface MetricGroup
{
    /**
     * Return an array of metric definitions in this group.
     *
     * @return array<int, MetricDefinition>
     */
    public function definitions(): array;
}
