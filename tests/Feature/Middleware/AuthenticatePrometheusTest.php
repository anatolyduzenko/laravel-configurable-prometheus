<?php

namespace Tests\Feature\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use AnatolyDuzenko\ConfigurablePrometheus\Http\Middleware\AuthenticatePrometheus;
use Tests\BaseTestCase;

/**
 * Class AuthenticatePrometheusTest
 *
 * Tests the authentication middleware that protects the metrics endpoint.
 */
class AuthenticatePrometheusTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    protected function createRequestWithAuth($user = null, $pass = null): Request
    {
        $server = [];
        if ($user !== null && $pass !== null) {
            $server['PHP_AUTH_USER'] = $user;
            $server['PHP_AUTH_PW'] = $pass;
        }

        return Request::create('/test-metrics', 'GET', [], [], [], $server);
    }

    /**
     * Ensure that unauthorized users cannot access the protected route.
     */
    public function test_denies_request_without_credentials()
    {
        $middleware = new AuthenticatePrometheus();
        $request = $this->createRequestWithAuth();

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($response->headers->has('WWW-Authenticate'));
    }

    /**
     * Ensure that access is allowed with valid credentials.
     */
    public function test_allows_request_with_valid_credentials()
    {
        $middleware = new AuthenticatePrometheus();
        $request = $this->createRequestWithAuth('admin', 'secret');

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Ensure that access is denied with incorrect credentials.
     */
    public function test_denies_request_with_wrong_credentials()
    {
        $middleware = new AuthenticatePrometheus();
        $request = $this->createRequestWithAuth('wrong', 'bad');

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals(401, $response->getStatusCode());
    }
}
