<?php
use Framework\Container\LaravelContainer;
use League\Container\Argument\RawArgument;

$container = new League\Container\Container();

$laravel_container = new LaravelContainer($container);
$container->delegate(
    new \League\Container\ReflectionContainer
);

// Add container itself
$container->add('League\Container\Container', $container);
$container->add('Illuminate\Container\Container', $laravel_container);
$container->add('Illuminate\Contracts\Container\Container', $laravel_container);

// Add service providers
$container->addServiceProvider(\App\ServiceProvider\ConfigServiceProvider::class);
$container->addServiceProvider(\Spekkionu\DomainDispatcher\DispatcherServiceProvider::class);
$container->addServiceProvider(\App\ServiceProvider\SessionServiceProvider::class);
$container->addServiceProvider(\App\ServiceProvider\ViewServiceProvider::class);
$container->addServiceProvider(\App\ServiceProvider\HttpServiceProvider::class);
$container->addServiceProvider(\App\ServiceProvider\FtpServiceProvider::class);


return $container;
