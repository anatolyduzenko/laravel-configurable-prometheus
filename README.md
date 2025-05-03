# Laravel Configurable Prometheus

[![Run Tests](https://github.com/anatolyduzenko/laravel-configurable-prometheus/actions/workflows/run-tests.yml/badge.svg)](https://github.com/anatolyduzenko/laravel-configurable-prometheus/actions/workflows/run-tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/aduzenko/laravel-configurable-prometheus.svg?style=flat-square)](https://packagist.org/packages/aduzenko/laravel-configurable-prometheus)
[![License](https://img.shields.io/github/license/aduzenko/laravel-configurable-prometheus.svg?style=flat-square)](LICENSE)

> A Laravel package for defining, managing, and exporting Prometheus metrics in a flexible, extensible way.

---

## ✨ Features

- Simple configuration-driven metric definitions
- Full support for **Counter**, **Gauge**, **Histogram**, and **Summary**
- Designed for **Laravel 12** and PHP 8.3+
- Define metrics in your app, not just in the package

---

## 🚀 Installation

```bash
composer require aduzenko/laravel-configurable-prometheus
```

---

## ⚙️ Publishing config

```bash
php artisan vendor:publish --tag=prometheus-config
```

This will publish:

- `config/prometheus.php`
- example metrics route

---

### 🔐 Authentication for report route

The Prometheus endpoint is protected from unauthorized access by basic HTTP authentication.

#### Step 1: Add credentials to your `.env` file

```env
PROMETHEUS_USER=prom
PROMETHEUS_PASSWORD=secret
```

---

### 📡 Route setup for Prometheus metrics

The Prometheus endpoint is configurable.

#### Update route in `config/prometheus.php` file

```php
'endpoint' => 'prometheus',
```

---

## 🧩 Defining custom metrics

Define a class implementing `MetricGroup`:

```php
namespace App\Metrics;

use AnatolyDuzenko\ConfigurablePrometheus\DTO\MetricDefinition;
use AnatolyDuzenko\ConfigurablePrometheus\Enums\MetricType;
use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricGroup;

class ApiMetrics implements MetricGroup
{
    public function definitions(): array
    {
        return [
            new MetricDefinition(
                namespace: 'api',
                name: 'response_time_seconds',
                helpText: 'API response time',
                type: MetricType::Histogram,
                labelNames: ['route'],
                buckets: [0.1, 0.3, 0.5, 1, 2, 5]
            )
        ];
    }
}
```

Then reference your group in `config/prometheus.php`:

```php
'groups' => [
    \App\Metrics\ApiMetrics::class,
],
```

---

## 📈 Usage

```php
// In your class constructor, use
public function __construct(protected MetricManager $metrics)
    {}
// then
$this->metrics->inc('user_logins_total', ['web']);
$this->metrics->set('active_users', 42, ['web']);
$this->metrics->observe('api', 'response_time_seconds', 0.32, ['/api/v1']);
```

### Alternate usage

```php
// In your method
public function index(Request $request, MetricManager $metrics)
{
    // ....
    $metrics->inc('user_logins_total', ['web']);
    $metrics->set('active_users', 42, ['web']);
    $metrics->observe('api', 'response_time_seconds', 0.32, ['/api/v1']);
}
```

---

## 🧪 Testing

```bash
vendor/bin/phpunit
```

---

## 📄 License

MIT © Anatoly Duzenko
