<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Prometheus Storage Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "redis", "in_memory"
    |
    */

    'driver' => env('PROMETHEUS_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | If using Redis driver, you may customize the Laravel Redis connection.
    |
    */

    'redis_connection' => env('PROMETHEUS_REDIS_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Groups
    |--------------------------------------------------------------------------
    |
    | Define your metrics classes here.
    |
    */
    'groups' => [
        \AnatolyDuzenko\ConfigurablePrometheus\Metrics\Groups\UserMetrics::class,
    ],
    

    /*
    |--------------------------------------------------------------------------
    | Metrics Authorization
    |--------------------------------------------------------------------------
    |
    | Define your user and password in Laravel's application config.
    |
    */
    'auth' => [
        'user' => env('PROMETHEUS_USER'),
        'password' => env('PROMETHEUS_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Endpoint
    |--------------------------------------------------------------------------
    |
    | Define your metrics endpoint here.
    |
    */
    'endpoint' => 'prometheus',

];
