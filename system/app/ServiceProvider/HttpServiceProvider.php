<?php

namespace App\ServiceProvider;

use App\Middleware\AuthenticatedMiddleware;
use App\Middleware\CheckCsrfMiddleware;
use App\Middleware\StartSessionMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class HttpServiceProvider
 *
 * @package DigitalCanvas\ServiceProvider
 */
class HttpServiceProvider extends AbstractServiceProvider
{
    /**
     * This array allows the container to be aware of
     * what your service provider actually provides,
     * this should contain all alias names that
     * you plan to register with the container
     *
     * @var array
     */
    protected $provides
        = [
            Request::class,
            'Symfony\Component\HttpFoundation\Request',
            'Illuminate\Routing\Router',
            'Illuminate\Routing\Redirector',
            'Illuminate\Routing\UrlGenerator',
        ];

    /**
     * Default namespace for controllers
     * @var string
     */
    protected $namespace = 'App\Controller';

    /**
     * Middleware applied to all routes
     * @var array
     */
    protected $middlewareGlobal = [
        StartSessionMiddleware::class,
        CheckCsrfMiddleware::class,
    ];

    /**
     * Available middleware
     * @var array
     */
    protected $middleware = [
        'auth' => AuthenticatedMiddleware::class
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to
     */
    public function register()
    {
        $this->getContainer()->share(
            Request::class, function () {
            return Request::capture();
        }
        );

        $this->getContainer()->add(
            'Symfony\Component\HttpFoundation\Request', function () {
            return $this->getContainer()->get(Request::class);
        }
        );

        $this->getContainer()->share(
            'Illuminate\Routing\ResponseFactory', function () {
                $view = $this->getContainer()->get('Illuminate\View\Factory');
                $redirector = $this->getContainer()->get('Illuminate\Routing\Redirector');
            return new ResponseFactory($view, $redirector);
        }
        );

        $this->getContainer()->add(
            'Illuminate\Contracts\Routing\ResponseFactory', function () {
            return $this->getContainer()->get('Illuminate\Routing\ResponseFactory');
        }
        );

        $this->getContainer()->share(
            'Illuminate\Routing\Router', function () {
            $router = new Router(
                $this->getContainer()->get('Illuminate\Events\Dispatcher'),
                $this->getContainer()->get('Illuminate\Container\Container')
            );

            \Route::setInstance($router);

            foreach ($this->middleware as $key => $middleware) {
                $router->aliasMiddleware($key, $middleware);
            }

            // Include Routes
            $router->namespace($this->namespace)->middleware($this->middlewareGlobal)->group(function() use ($router) {
                require_once SYSTEM_DIR . '/configs/routes.php';
            });

            return $router;
        }
        );

        $this->getContainer()->share(
            'Illuminate\Routing\Redirector', function () {
            return new Redirector($this->getContainer()->get('Illuminate\Routing\UrlGenerator'));
        }
        );

        $this->getContainer()->share(
            'Illuminate\Routing\UrlGenerator', function () {
            /** @var \Illuminate\Routing\Router $router */
            $router = $this->getContainer()->get('Illuminate\Routing\Router');
            /** @var \Illuminate\Http\Request $request */
            $request = $this->getContainer()->get('Illuminate\Http\Request');

            return new UrlGenerator($router->getRoutes(), $request);
        }
        );
    }
}
