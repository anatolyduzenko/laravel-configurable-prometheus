<?php

use Illuminate\Support\Facades\Route;
use Prometheus\RenderTextFormat;
use Prometheus\CollectorRegistry;

Route::middleware('prometheus.auth')->get(config('prometheus.endpoint'), function (CollectorRegistry $registry) {
    $renderer = new RenderTextFormat();

    $result = $registry->getMetricFamilySamples();
    $output = $renderer->render($result);

    return response($output, 200)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});
