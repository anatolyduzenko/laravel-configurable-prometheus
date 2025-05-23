<?php

namespace AnatolyDuzenko\ConfigurablePrometheus\Providers;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;
use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricManagerInterface;
use AnatolyDuzenko\ConfigurablePrometheus\Services\MetricManager;
use AnatolyDuzenko\ConfigurablePrometheus\Support\RedisAdapterFactory;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;

/**
 * Class MetricsServiceProvider
 *
 * Laravel service provider that loads metric configuration, routes,
 * and registers Prometheus metric groups and registry.
 */
class MetricsServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the service container.
     *
     * Here we bind MetricManager and CollectorRegistry as singletons.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/prometheus.php', 'prometheus');

        $this->app->singleton(CollectorRegistry::class, function () {
            $adapter = RedisAdapterFactory::make();

            return new CollectorRegistry($adapter);
        });

        $this->app->singleton(MetricManagerInterface::class, fn ($app) => new MetricManager($app->make(CollectorRegistry::class)));
    }

    /**
     * Bootstrap any application services.
     *
     * This loads routes, publishes config, and initializes metric registration.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/prometheus.php' => config_path('prometheus.php'),
        ]);

        $this->app['router']->aliasMiddleware('prometheus.auth', \AnatolyDuzenko\ConfigurablePrometheus\Http\Middleware\AuthenticatePrometheus::class);

        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        if (config('prometheus.enabled', true)) {
            $this->registerMetrics();
        } else {
            $this->bindFakeMetricManager();
        }
    }

    /**
     * Register Mertics in application
     * @return void
     */
    private function registerMetrics(): void {
        $this->app->afterResolving(MetricManager::class, function (MetricManager $metrics) {

            $groupClasses = config('prometheus.groups', []);
            $metricGroups = collect($groupClasses)
                ->map(fn ($class) => app($class))
                ->filter(fn ($instance) => $instance instanceof MetricGroup)
                ->all();

            $metrics->register($metricGroups);
        });

        if ($this->app->runningInConsole()) {
            $this->app->make(MetricManager::class);
        }
    }

    /**
     * Bind fake dummy for tests
     * @return void
     */
    private function bindFakeMetricManager(): void {
        $this->app->singleton(MetricManagerInterface::class, function () {
            return new class implements MetricManagerInterface {
                public function register(array $metricGroups): void {}
                public function set(string $namespace, string $name, float|int $value, array $labels = []): void {}
                public function dec(string $namespace, string $name, array $labelValues = []): void {}
                public function inc(string $namespace, string $name, array $labels = []): void {}
                public function observe(string $namespace, string $name, float $value, array $labelValues = []): void {}
                public function observeSummary(string $namespace, string $name, float $value, array $labelValues = []): void {}
            };
        });
    }
}
