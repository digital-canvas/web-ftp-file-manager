<?php

namespace App\ServiceProvider;

use Illuminate\Config\Repository;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * Class ConfigServiceProvider
 *
 * @package App\ServiceProvider
 */
class ConfigServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
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
            'config',
            'Illuminate\Config\Repository',
            'Illuminate\Contracts\Config\Repository'
        ];
    public function boot()
    {
        $this->getContainer()->share(
            'Illuminate\Config\Repository',
            function () {
                return new Repository(require SYSTEM_DIR . '/configs/config.php');
            }
        );
    }

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register() {
        $this->getContainer()->add(
            'Illuminate\Contracts\Config\Repository',
            function () {
                return $this->getContainer()->get('Illuminate\Config\Repository');
            }
        );
        $this->getContainer()->add(
            'config',
            function () {
                return $this->getContainer()->get('Illuminate\Config\Repository');
            }
        );
    }
}
