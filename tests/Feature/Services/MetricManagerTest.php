<?php

namespace Tests\Feature\Services;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;
use AnatolyDuzenko\ConfigurablePrometheus\DTO\MetricDefinition;
use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;
use AnatolyDuzenko\ConfigurablePrometheus\Services\MetricManager;
use Mockery;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Gauge;
use Tests\BaseTestCase;

/**
 * Class MetricManagerTest
 *
 * Tests the MetricManager logic for registering and interacting with metrics.
 */
class MetricManagerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that MetricManager correctly registers metrics from a MetricGroup.
     */
    public function test_it_registers_metrics()
    {
        $definition = new MetricDefinition(
            namespace: 'tests',
            name: 'test_counter',
            helpText: 'A test counter',
            type: MetricType::Counter,
            labelNames: ['env']
        );

        $group = Mockery::mock(MetricGroup::class);
        $group->shouldReceive('definitions')->andReturn([$definition]);

        $registry = Mockery::mock(CollectorRegistry::class);
        $registry->shouldReceive('getOrRegisterCounter')
            ->with('tests', 'test_counter', 'A test counter', ['env'])
            ->once();

        $manager = new MetricManager($registry);
        $manager->register([$group]);

        $this->assertInstanceOf(MetricManager::class, $manager);
    }

    /**
     * Test that MetricManager can increment and set metric values.
     */
    public function test_it_sets_and_increments()
    {
        $counter = Mockery::mock(Counter::class);
        $counter->shouldReceive('inc')->with(['app'])->once();

        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('set')->with(5, ['app'])->once();

        $registry = Mockery::mock(CollectorRegistry::class);
        $registry->shouldReceive('getCounter')
            ->with('tests', 'counter_name')
            ->andReturn($counter);
        $registry->shouldReceive('getGauge')
            ->with('tests', 'counter_name')
            ->andReturn($gauge);
        $registry->shouldReceive('getGauge')
            ->with('tests', 'gauge_name')
            ->andReturn($gauge);

        $manager = new MetricManager($registry);
        $manager->inc('tests', 'counter_name', ['app']);
        $manager->set('tests', 'gauge_name', 5, ['app']);

        $this->assertInstanceOf(MetricManager::class, $manager);
    }

    /**
     * Close Mockery after each test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
