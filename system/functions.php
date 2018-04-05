<?php

use Framework\Authenticator;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\HtmlString;

/**
 * Returns DI container or pulls object from container
 *
 * @param null|string $key
 * @param array $args
 *
 * @return mixed|\League\Container\Container
 */
function app($key = null, array $args = [])
{
    global $container;
    if (is_null($key)) {
        return $container;
    }

    return $container->get($key, $args);
}

if ( ! function_exists('config')) {
    /**
     * Returns config variable
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = app('Illuminate\Config\Repository');

        if (is_null($key)) {
            return $config;
        }

        if (is_array($key)) {
            return $config->set($key);
        }

        return $config->get($key, $default);
    }
}

if ( ! function_exists('dispatch')) {
    /**
     * Executes the given command and optionally returns a value
     *
     * @param object $command
     *
     * @return mixed
     */
    function dispatch($command)
    {
        /** @var \Spekkionu\DomainDispatcher\Dispatcher $dispatcher */
        $dispatcher = app('Spekkionu\DomainDispatcher\Dispatcher');

        return $dispatcher->dispatch($command);
    }
}

if ( ! function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string $key
     * @param  mixed $default
     *
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('Illuminate\Http\Request');
        }
        if (is_array($key)) {
            return app('Illuminate\Http\Request')->only($key);
        }
        $value = app('Illuminate\Http\Request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if ( ! function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  string $content
     * @param  int $status
     * @param  array $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function response($content = '', $status = 200, array $headers = [])
    {
        /** @var \Illuminate\Contracts\Routing\ResponseFactory $factory */
        $factory = app('Illuminate\Contracts\Routing\ResponseFactory');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}
if ( ! function_exists('current_url')) {
    /**
     * Returns current url
     *
     * @return string
     */
    function current_url()
    {
        return request()->getUri();
    }
}
if ( ! function_exists('url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string $path
     * @param  mixed $parameters
     * @param  bool $secure
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }
}
if ( ! function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  string $name
     * @param  array $parameters
     * @param  bool $absolute
     *
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        return app(UrlGenerator::class)->route($name, $parameters, $absolute);
    }
}
if ( ! function_exists('session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Session\Session
     */
    function session($key = null, $default = null)
    {
        /** @var Symfony\Component\HttpFoundation\Session\Session $session */
        $session = app('Symfony\Component\HttpFoundation\Session\Session');
        if (is_null($key)) {
            return $session;
        }
        if (is_array($key)) {
            return $session->replace($key);
        }

        return $session->get($key, $default);
    }
}
if ( ! function_exists('csrf_token')) {
    /**
     * Returns csrf token value
     *
     * @return string
     */
    function csrf_token()
    {
        $key     = 'csrf_token';
        $session = session();
        if ( ! $session->has($key)) {
            $token = bin2hex(random_bytes(16));
            $session->set($key, $token);
        } else {
            $token = $session->get($key);
        }

        return $token;
    }
}
if ( ! function_exists('csrf_field')) {
    /**
     * Returns csrf token input field
     *
     * @return string|HtmlString
     */
    function csrf_field()
    {
        $token = csrf_token();

        return new HtmlString("<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">");
    }
}
if ( ! function_exists('auth')) {
    /**
     * Generate a url for the application.
     *
     * @return Authenticator
     */
    function auth()
    {
        return app(Authenticator::class);
    }
}
if ( ! function_exists('format_date')) {
    /**
     * Returns DI container or pulls object from container
     *
     * @param mixed $date
     * @param string $format
     * @param string|DateTimeZone $timezone
     *
     * @return string|null
     */
    function format_date($date, string $format = 'm/d/Y h:i:s A', $timezone = null)
    {
        if (empty($date)) {
            return null;
        }
        if (is_string($date)) {
            $date = new DateTime($date, $timezone);

            return $date->format($format);
        }
        if ( ! ($date instanceof \DateTime)) {
            return null;
        }
        if ($timezone) {
            $date = clone $date;
            $date->setTimezone($timezone);
        }

        return $date->format($format);
    }
}
if ( ! function_exists('query_string')) {
    /**
     * Requires parameters as query string
     *
     * @param array $params An array with key=>value to use as the url parameters
     * @param boolean $reset If true do not include current query string parameters
     * @param null|string $current The current query string. If not provided $_SERVER['QUERY_STRING'] will be used
     *
     * @return string query string. Does not include ?
     */
    function query_string(array $params = [], $reset = false, $current = null)
    {
        if ( ! $reset) {
            $current = ! is_null($current) ? $current : $_SERVER['QUERY_STRING'];
            parse_str($current, $query);
        } else {
            $query = [];
        }
        $query = array_merge($query, $params);

        return ($query) ? http_build_query($query) : null;
    }
}

/**
 * @param string $path
 *
 * @return string
 */
function asset(string $path): string
{
    static $manifest;
    if ( ! $manifest) {
        if (file_exists(dirname(__DIR__) . '/assets/build/manifest.json')) {
            $manifest = json_decode(file_get_contents(dirname(__DIR__) . '/assets/build/manifest.json'), true);
        } else {
            $manifest = [];
        }
    }
    if (array_key_exists($path, $manifest)) {
        return $manifest[$path];
    }

    return $path;
}

if ( ! function_exists('redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null $to
     * @param  int $status
     * @param  array $headers
     * @param  bool $secure
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return app('Illuminate\Routing\Redirector');
        }

        return app('Illuminate\Routing\Redirector')->to($to, $status, $headers, $secure);
    }
}
/**
 * @param string $template
 * @param array $params
 * @param array $mergeData
 *
 * @return \Illuminate\Contracts\View\View|\Illuminate\View\Factory
 */
function view(string $template, array $params = [], array $mergeData = [])
{
    /** @var \Illuminate\View\Factory $view */
    $view = app('Illuminate\View\Factory');
    if (func_num_args() === 0) {
        return $view;
    }

    return $view->make($template, $params, $mergeData);
}
if (!function_exists('bytesToSize')) {

    /**
     * Convert bytes to human readable format
     *
     * @param integer $bytes Size in bytes to convert
     * @param int $precision
     *
     * @return string
     */
    function bytesToSize($bytes, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';

        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';

        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';

        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';

        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }
}
/**
 * @param string $val
 *
 * @return int
 */
function return_bytes($val)
{
    $val  = trim($val);

    $last = strtolower($val[strlen($val)-1]);
    $val  = substr($val, 0, -1); // necessary since PHP 7.1; otherwise optional

    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return (int) $val;
}
