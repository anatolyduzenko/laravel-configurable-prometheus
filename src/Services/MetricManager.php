<?php 

namespace AnatolyDuzenko\ConfigurablePrometheus\Services;

use Prometheus\CollectorRegistry;
use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;
use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;
use Illuminate\Support\Collection;

final class MetricManager
{
    /**
     * Create a new MetricManager instance.
     *
     * @param \Prometheus\CollectorRegistry $registry
     */
    public function __construct( protected CollectorRegistry $collectorRegistry)
    {
    }

    /** @param MetricGroup[] $groups */
    /**
     * Register all metric definitions from provided groups.
     *
     * @param array<int, \AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup> $groups
     */
    public function register(array $groups): void
    {
        $allDefinitions = collect($groups)
            ->flatMap(fn($group) => $group->definitions());

        foreach ($allDefinitions as $definition) {
            match ($definition->type) {
                MetricType::Counter => $this->collectorRegistry->getOrRegisterCounter(
                    $definition->namespace, 
                    $definition->name, 
                    $definition->helpText, 
                    $definition->labelNames
                ),
                MetricType::Gauge => $this->collectorRegistry->getOrRegisterGauge(
                    $definition->namespace, 
                    $definition->name, 
                    $definition->helpText, 
                    $definition->labelNames
                ),
                MetricType::Histogram => $this->collectorRegistry->getOrRegisterHistogram(
                    $definition->namespace, 
                    $definition->name, 
                    $definition->helpText, 
                    $definition->labelNames, 
                    $definition->buckets
                ),
                MetricType::Summary => $this->collectorRegistry->getOrRegisterSummary(
                    $definition->namespace, 
                    $definition->name, 
                    $definition->helpText, 
                    $definition->labelNames
                ),
                default => throw new \InvalidArgumentException("Unsupported type: {$definition->type->value}"),
            };
        }
    }

    /**
     * Increment a counter or gauge metric by 1.
     *
     * @param string $name
     * @param array<int, string> $labelValues
     */
    public function inc(string $namespace, string $name, array $labelValues = []): void
    {
        $result = rescue(function () use ($namespace, $name, $labelValues) {
            $this->collectorRegistry->getCounter($namespace, $name)->inc($labelValues);
        }, function () use ($namespace, $name, $labelValues) {
            $this->collectorRegistry->getGauge($namespace, $name)->inc($labelValues);
        }, report: false);
    }

    /**
     * Decrement a gauge metric by 1.
     *
     * @param string $name
     * @param array<int, string> $labelValues
     */
    public function dec(string $namespace, string $name, array $labelValues = []): void
    {
        $this->collectorRegistry->getGauge($namespace, $name)->dec($labelValues);
    }

    /**
     * Set the value of a gauge metric.
     *
     * @param string $name
     * @param float|int $value
     * @param array<int, string> $labelValues
     */
    public function set(string $namespace, string $name, float|int $value, array $labelValues = []): void
    {
        $this->collectorRegistry->getGauge($namespace, $name)->set($value, $labelValues);
    }

    /**
     * Observe a value for a histogram metric.
     *
     * @param string $name
     * @param float $value
     * @param array<int, string> $labelValues
     */
    public function observe(string $namespace, string $name, float $value, array $labelValues = []): void
    {
        $this->collectorRegistry
            ->getHistogram($namespace, $name)
            ->observe($value, $labelValues);
    }

    /**
     * Observe a value for a summary metric.
     *
     * @param string $name
     * @param float $value
     * @param array<int, string> $labelValues
     */
    public function observeSummary(string $namespace, string $name, float $value, array $labelValues = []): void
    {
        $this->collectorRegistry
            ->getSummary($namespace, $name)
            ->observe($value, $labelValues);
    }
}
