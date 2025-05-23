<?php

namespace AnatolyDuzenko\ConfigurablePrometheus\Contracts;

interface MetricManagerInterface
{
    public function register(array $metricGroups): void;

    public function set(string $namespace, string $name, float|int $value, array $labels = []): void;

    public function dec(string $namespace, string $name, array $labelValues = []): void;
    
    public function inc(string $namespace, string $name, array $labels = []): void;
    
    public function observe(string $namespace, string $name, float $value, array $labelValues = []): void;

    public function observeSummary(string $namespace, string $name, float $value, array $labelValues = []): void;

}