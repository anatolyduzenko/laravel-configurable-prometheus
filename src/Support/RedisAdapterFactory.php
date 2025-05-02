<?php 

namespace AnatolyDuzenko\ConfigurablePrometheus\Support;

use Prometheus\Storage\Redis;

/**
 * Class RedisAdapterFactory
 *
 * Creates and returns a Redis adapter compatible with Prometheus.
 * This adapter uses the native PHP Redis extension to connect.
 */
class RedisAdapterFactory
{
    /**
     * Create a Redis adapter for Prometheus storage.
     *
     * @return \Prometheus\Storage\Redis
     */
    public static function make(): Redis
    {
        return new Redis([
            'host' => config('database.redis.default.host', '127.0.0.1'),
            'port' => config('database.redis.default.port', 6379),
            'timeout' => 0.1,
            'read_timeout' => 10,
            'persistent_connections' => false,
        ]);
    }
}