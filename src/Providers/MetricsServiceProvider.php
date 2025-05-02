<?php 

namespace AnatolyDuzenko\ConfigurablePrometheus\Providers;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;
use AnatolyDuzenko\ConfigurablePrometheus\Services\MetricManager;
use Illuminate\Support\ServiceProvider;
use AnatolyDuzenko\ConfigurablePrometheus\Support\RedisAdapterFactory;
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
        $this->mergeConfigFrom(__DIR__ . '/../config/prometheus.php', 'prometheus');

        $this->app->singleton(CollectorRegistry::class, function () {
            $adapter = RedisAdapterFactory::make();
            return new CollectorRegistry($adapter);
        });

        $this->app->singleton(MetricManager::class, fn ($app) => new MetricManager($app->make(CollectorRegistry::class)));
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

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        $this->app->afterResolving(MetricManager::class, function (MetricManager $metrics) {
            
            $groupClasses = config('prometheus.groups', []);
            $metricGroups = collect($groupClasses)
                ->map(fn($class) => app($class))
                ->filter(fn($instance) => $instance instanceof MetricGroup)
                ->all();
            
            $metrics->register($metricGroups);
        });
        
        if ($this->app->runningInConsole()) {
            $this->app->make(MetricManager::class);
        }
    }
}
