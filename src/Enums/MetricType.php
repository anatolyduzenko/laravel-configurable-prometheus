<?php

namespace AnatolyDuzenko\ConfigurablePrometheus\Enums;

/**
 * Enum MetricType
 *
 * Defines available metric types supported by Prometheus.
 */
enum MetricType: string
{
    case Counter = 'counter';
    case Gauge = 'gauge';
    case Histogram = 'histogram';
    case Summary = 'summary';
}
