<?php

namespace App\ServiceProvider;

use Framework\Ftp\FtpManager;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class FtpServiceProvider
 *
 * @package App\ServiceProvider
 */
class FtpServiceProvider extends AbstractServiceProvider
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
            FtpManager::class
        ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to
     */
    public function register()
    {
        $this->getContainer()->share(
            FtpManager::class, function () {
            $config = $this->getContainer()->get('Illuminate\Config\Repository');
            $server = $config->get('ftp.server', 'localhost');
            $port   = $config->get('ftp.port', 21);
            $passive   = $config->get('ftp.passive', false);
            $ssl   = $config->get('ftp.ssl', false);

            return new FtpManager($server, $port, $passive, $ssl);
        }
        );

    }
}
