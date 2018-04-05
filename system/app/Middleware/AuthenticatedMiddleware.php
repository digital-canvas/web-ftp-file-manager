<?php

namespace App\Middleware;

use Closure;
use Framework\Authenticator;

/**
 * Class AuthenticatedMiddleware
 *
 * @package App\Middleware
 */
class AuthenticatedMiddleware
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * AuthenticatedMiddleware constructor.
     *
     * @param Authenticator $authenticator
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

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
        $authenticated = $this->authenticator->check();

        if ( ! $authenticated) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            session()->set('_previous', $request->url());

            return redirect('login');
        }

        return $next($request);
    }
}
