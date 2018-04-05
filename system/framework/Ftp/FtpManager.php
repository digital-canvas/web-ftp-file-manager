<?php

namespace Framework\Ftp;

/**
 * Class FtpManager
 *
 * @package Framework\Ftp
 */
class FtpManager
{
    /**
     * @var string
     */
    private $server;
    /**
     * @var int
     */
    private $port;

    /**
     * @var resource
     */
    private $conn;

    /**
     * @var bool
     */
    private $logged_in = false;
    /**
     * @var bool
     */
    private $passive = false;
    /**
     * @var bool
     */
    private $ssl = false;

    /**
     * FtpManager constructor.
     *
     * @param string $server
     * @param int $port
     * @param bool $passive
     * @param bool $ssl
     */
    public function __construct(string $server = 'localhost', int $port = 21, bool $passive = false, bool $ssl = false)
    {
        $this->server  = $server;
        $this->port    = $port;
        $this->passive = $passive;
        $this->ssl     = $ssl;
    }

    /**
     * @return resource
     */
    public function connection()
    {
        if ($this->conn) {
            return $this->conn;
        }

        return $this->connect();
    }

    /**
     * @return resource
     */
    public function connect()
    {
        $this->close();
        if ($this->ssl) {
            $this->conn = @ftp_ssl_connect($this->server, $this->port);
        } else {
            $this->conn = @ftp_connect($this->server, $this->port);
        }

        return $this->conn;
    }

    /**
     * @return bool
     */
    public function close()
    {
        if ($this->conn) {
            return @ftp_close($this->conn);
        }

        return true;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function login(string $username, string $password)
    {
        $this->logged_in = @ftp_login($this->connection(), $username, $password);
        if ($this->logged_in && $this->passive) {
            @ftp_pasv($this->connection(), $this->passive);
        }

        return $this->logged_in;
    }

    /**
     * @return bool
     */
    public function authenticated()
    {
        return $this->logged_in;
    }

    /**
     * @return string
     */
    public function currentDirectory()
    {
        return @ftp_pwd($this->connection());
    }

    /**
     * @return bool
     */
    public function upDirectory()
    {
        return @ftp_cdup($this->connection());
    }

    /**
     * @param string $directory
     *
     * @return bool
     */
    public function changeDirectory($directory = '/')
    {
        return @ftp_chdir($this->connection(), $directory);
    }

    /**
     * Returns files in directory
     *
     * @param string $directory
     *
     * @return array
     */
    public function files(string $directory = '.')
    {
        return @ftp_nlist($this->connection(), $directory);
    }

    /**
     * Returns details list of files in directory
     *
     * @param string $directory
     *
     * @return array
     */
    public function listFiles(string $directory = '.')
    {
        return @ftp_mlsd($this->connection(), $directory);
    }

    /**
     * Raw listing of files in a directory
     * Same format at `ls` command
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function rawlist(string $directory = '.', bool $recursive = false)
    {
        return @ftp_rawlist($this->connection(), $directory, $recursive);
    }

    /**
     * Returns list of files and directories
     * Only array of filenames
     *
     * @param string $directory
     *
     * @return array
     */
    public function nlist(string $directory = '.')
    {
        return @ftp_nlist($this->connection(), $directory);
    }

    /**
     * @param string $directory
     *
     * @return string
     */
    public function mkdir(string $directory)
    {
        return @ftp_mkdir($this->connection(), $directory);
    }

    /**
     * @param string $directory
     *
     * @return bool
     */
    public function rmdir(string $directory)
    {
        return @ftp_rmdir($this->connection(), $directory);
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function rm(string $file)
    {
        return @ftp_delete($this->connection(), $file);
    }

    /**
     * Uploads an open file
     *
     * @param resource $stream
     * @param string $filename
     *
     * @return bool
     */
    public function upload($stream, string $filename)
    {
        return @ftp_fput($this->connection(), $filename, $stream, FTP_BINARY, 0);
    }

    /**
     * @param string $path
     *
     * @return int
     */
    public function size(string $path)
    {
        return @ftp_size($this->connection(), $path);
    }

    /**
     * @param string $path
     *
     * @return int
     */
    public function modified(string $path)
    {
        return @ftp_mdtm($this->connection(), $path);
    }

    /**
     * @param string $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        array_unshift($arguments, $this->connection());
        $function = 'ftp_' . $name;

        return call_user_func_array($function, $arguments);
    }
}
