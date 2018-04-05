<?php

namespace Framework;

use Framework\Ftp\FtpManager;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Authenticator
 *
 * @package Framework
 */
class Authenticator
{
    /**
     * @var Session
     */
    private $session;

    private $namespace = '_auth';
    /**
     * @var FtpManager
     */
    private $ftp;

    /**
     * Authenticator constructor.
     *
     * @param Session $session
     * @param FtpManager $ftp
     */
    public function __construct(Session $session, FtpManager $ftp)
    {
        $this->session = $session;
        $this->ftp     = $ftp;
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->ftp->authenticated()) {
            return true;
        }
        if ( ! $this->session->has($this->namespace)) {
            return false;
        }
        $username = $this->session->get($this->namespace)['username'];
        $password = $this->session->get($this->namespace)['password'];

        return $this->ftp->login($username, $password);
    }

    /**
     * @param $username
     * @param $password
     *
     * @return bool
     */
    public function login($username, $password)
    {
        if ( ! $this->ftp->login($username, $password)) {
            $this->session->remove($this->namespace);
            return false;
        }

        $this->session->set($this->namespace, [
            'username' => $username,
            'password' => $password,
        ]);

        return true;
    }

    /**
     * Logs out user
     */
    public function logout()
    {
        $this->ftp->close();
        $this->session->remove($this->namespace);
    }

    /**
     * @return FtpManager
     */
    public function ftp()
    {
        return $this->ftp;
    }

    /**
     * @return array
     */
    public function user()
    {
        if ( ! $this->check()) {
            return [
                'username' => null,
                'password' => null,
            ];
        }

        return $this->session->get($this->namespace);
    }

    /**
     * @return null|string
     */
    public function username()
    {
        if ( ! $this->check()) {
            return null;
        }

        return $this->session->get($this->namespace)['username'];
    }

    /**
     * @return null|string
     */
    public function password()
    {
        if ( ! $this->check()) {
            return null;
        }

        return $this->session->get($this->namespace)['password'];
    }
}
