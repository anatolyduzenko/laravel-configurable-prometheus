<?php 

namespace AnatolyDuzenko\ConfigurablePrometheus\DTO;

use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;

/**
 * Class MetricDefinition
 *
 * Data Transfer Object that describes a Prometheus metric.
 */
readonly class MetricDefinition
{
    /**
     * MetricDefinition constructor.
     *
     * @param string $name The metric name (without namespace).
     * @param string $helpText Description of the metric.
     * @param MetricType $type The type of metric: counter, gauge, histogram, summary.
     * @param array<int, string> $labelNames The label names required by the metric.
     * @param array<int, float> $buckets Buckets used for histogram metrics.
     * @param string|null $namespace Optional metric namespace.
     */
    public function __construct(
        public string $namespace,
        public string $name,
        public string $helpText,
        public MetricType $type,
        public array $labelNames = [],
        public array $buckets = []
    ) {}
}
