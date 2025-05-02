<?php

namespace Tests\Feature\Middleware;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Class AuthenticatePrometheusTest
 *
 * Tests the authentication middleware that protects the metrics endpoint.
 */
class AuthenticatePrometheusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('prometheus.auth')->get('/test-metrics', function () {
            return 'OK';
        });
    }

    /**
     * Ensure that unauthorized users cannot access the protected route.
     */
    public function test_denies_request_without_credentials()
    {
        $response = $this->get('/test-metrics');
        $response->assertStatus(401);
        $response->assertHeader('WWW-Authenticate');
    }

    /**
     * Ensure that access is allowed with valid credentials.
     */
    public function test_allows_request_with_valid_credentials()
    {
        config()->set('prometheus.auth.user', 'testuser');
        config()->set('prometheus.auth.password', 'testpass');

        $response = $this->get('/test-metrics', [
            'PHP_AUTH_USER' => 'testuser',
            'PHP_AUTH_PW' => 'testpass',
        ]);

        $response->assertStatus(200);
        $response->assertSee('OK');
    }

    /**
     * Ensure that access is denied with incorrect credentials.
     */
    public function test_denies_request_with_wrong_credentials()
    {
        config()->set('prometheus.auth.user', 'testuser');
        config()->set('prometheus.auth.password', 'testpass');

        $response = $this->get('/test-metrics', [
            'PHP_AUTH_USER' => 'wrong',
            'PHP_AUTH_PW' => 'bad',
        ]);

        $response->assertStatus(401);
    }
}
