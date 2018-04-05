<?php

namespace App\Middleware;

use Closure;
use Framework\Exception\InvalidCsrfTokenException;
use Ramsey\Uuid\Uuid;

/**
 * Class CheckCsrfMiddleware
 *
 * @package App\Middleware
 */
class CheckCsrfMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = csrf_token();

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if ($request->input('csrf_token')) {
                if ($request->input('csrf_token') != $token) {
                    throw new InvalidCsrfTokenException();
                }
            } elseif ($request->hasHeader('X-CSRF-TOKEN')) {
                if ($request->header('X-CSRF-TOKEN') != $token) {
                    throw new InvalidCsrfTokenException();
                }
            } else {
                throw new InvalidCsrfTokenException();
            }

        }

        return $next($request);
    }
}
