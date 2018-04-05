<?php
namespace App\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

/**
 * Class SessionServiceProvider
 *
 * @package DigitalCanvas\ServiceProvider
 */
class SessionServiceProvider extends AbstractServiceProvider
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
            Session::class
        ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to
     */
    public function register()
    {
        $this->getContainer()->share(
            'Symfony\Component\HttpFoundation\Session\Session', function () {
            $session = new Session(new NativeSessionStorage([], new NativeFileSessionHandler()));

            return $session;
        }
        );
    }

}
