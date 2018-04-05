<?php

namespace App\Middleware;

use Closure;

/**
 * Class StartSessionMiddleware
 *
 * @package App\Middleware
 */
class StartSessionMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle( $request, Closure $next ) {
        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = app( 'Symfony\Component\HttpFoundation\Session\Session' );
        if ( ! $session->isStarted() ) {
            $session->start();
        }

        return $next( $request );
    }
}
