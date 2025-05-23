<?php

namespace AnatolyDuzenko\ConfigurablePrometheus\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthenticatePrometheus
 *
 * Middleware that protects the metrics endpoint with basic authentication.
 * Only requests with correct username and password will be allowed.
 */
class AuthenticatePrometheus
{
    /**
     * Handle an incoming request and check for basic authentication.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = config('prometheus.auth.user');
        $password = config('prometheus.auth.password');

        if (
            ! $request->getUser() ||
            ! $request->getPassword() ||
            $request->getUser() !== $user ||
            $request->getPassword() !== $password
        ) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Prometheus Metrics"',
            ]);
        }

        return $next($request);
    }
}
