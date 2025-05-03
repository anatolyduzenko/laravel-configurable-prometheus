<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Laravel Container and Facades
        $container = new Container();
        Container::setInstance($container);
        Facade::setFacadeApplication($container);

        // Register config (before anything else)
        $config = new Repository([
            'app.name' => 'test-app',
            'prometheus.auth.user' => 'admin',
            'prometheus.auth.password' => 'secret',
        ]);
        $container->instance('config', $config);
        Config::swap($config); // 👈 Required for config()

        // Create fake URL generator and redirector
        $request = Request::create('/');
        $routes = new RouteCollection();
        $urlGenerator = new UrlGenerator($routes, $request);
        $redirector = new Redirector($urlGenerator);

        // Create and bind ResponseFactory manually
        $viewFactory = new FakeViewFactory();
        $responseFactory = new ResponseFactory($viewFactory, $redirector);

        $container->instance(ResponseFactory::class, $responseFactory);
        $container->alias(ResponseFactory::class, 'Illuminate\Contracts\Routing\ResponseFactory');
    }
}
