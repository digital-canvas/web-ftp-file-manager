<?php

namespace Framework\Dispatcher;

use Framework\Exception\InvalidCsrfTokenException;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RouteDispatcher
 *
 * @package Framework\Dispatcher
 */
class RouteDispatcher
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var Session
     */
    private $session;

    protected $throw_exceptions = true;

    /** @var \Illuminate\Http\Request */
    private $request;

    /**
     * RouteDispatcher constructor.
     *
     * @param Container $container
     * @param Session $session
     */
    public function __construct(Container $container, Session $session)
    {
        $this->container        = $container;
        $this->session          = $session;
        $this->request          = $this->container->get('Illuminate\Http\Request');
        $this->throw_exceptions = (bool)$this->container->get('Illuminate\Config\Repository')->get('app.debug', true);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function dispatch()
    {

        /** @var \Illuminate\Routing\Router $router */
        $router = $this->container->get('Illuminate\Routing\Router');

        // Dispatch the request through the router
        try {
            return $router->dispatch($this->request);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @param \Exception $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleException(\Exception $e)
    {
        if ($e instanceof InvalidCsrfTokenException) {
            $this->saveOldRequest($this->request);

            return $this->getErrorResponse('Invalid CSRF Token', 403);
        } elseif ($e instanceof NotFoundHttpException) {
            return $this->getErrorResponse('Page not Found', 404);
        }

        if ($this->throw_exceptions) {
            if($this->request->expectsJson()){
                return response()->json(['success' => false, 'message' => $e->__toString()], 500);
            }
            throw $e;
        }

        return $this->getErrorResponse();
    }


    /**
     * Saves old input as flash data
     */
    protected function saveOldRequest()
    {
        if ( ! $this->request->expectsJson()) {
            $this->session->getFlashBag()->set('_old', $this->request->request->all());
        }
    }

    /**
     * @param string $message
     * @param int $status
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getErrorResponse($message = 'Application Error', $status = 500)
    {
        if ($this->request->isXmlHttpRequest()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }
        /** @var \Illuminate\View\Factory $view */
        $view = $this->container->get('Illuminate\View\Factory');
        if ($view->exists($status)) {
            return response(view($status, [])->render(), $status);
        }

        return response($message, $status);
    }
}
