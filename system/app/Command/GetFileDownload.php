<?php

namespace App\Command;

use App\Exception\FailedToDownloadFileException;
use Framework\Authenticator;
use Framework\Ftp\FtpManager;

/**
 * Class GetFileStream
 *
 * @package App\Command
 */
class GetFileDownload
{
    /**
     * @var string
     */
    private $path;

    /**
     * GetFileStream constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = '/' . ltrim($path, '/');
    }

    /**
     * @param Authenticator $authenticator
     * @param FtpManager $ftp
     *
     * @return array
     */
    public function handle(Authenticator $authenticator, FtpManager $ftp)
    {
        $username = rawurlencode($authenticator->username());
        $password = rawurlencode($authenticator->password());
        $scheme = config('ftp.ssl', false) ? 'ftps' : 'ftp';
        $server = config('ftp.server', 'localhost');
        $port = config('ftp.port', 21);

        $url = "{$scheme}://{$username}:{$password}@{$server}:{$port}{$this->path}";
        $stream = fopen($url, 'rb');
        if($stream === false){
            throw new FailedToDownloadFileException('Could not download file');
        }
        $filename = pathinfo($this->path, PATHINFO_BASENAME);
        $size = $ftp->size($this->path);
        $modified = gmdate('D, d M Y H:i:s T', $ftp->modified($this->path));
        return [
            'filename' => $filename,
            'stream' => $stream,
            'size' => $size,
            'modified' => $modified,
        ];
    }
}
