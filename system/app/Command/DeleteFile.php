<?php

namespace App\Command;

use App\Exception\FailedToDeleteFileException;
use Framework\Ftp\FtpManager;

/**
 * Class DeleteFile
 *
 * @package App\Command
 */
class DeleteFile
{
    /**
     * @var string
     */
    private $file;
    /**
     * @var string
     */
    private $current;

    /**
     * DeleteFile constructor.
     *
     * @param string $file
     * @param string $current
     */
    public function __construct(string $file, string $current = '.')
    {
        $this->file = $file;
        $this->current = $current;
    }

    /**
     * @param FtpManager $ftp
     *
     * @return bool
     */
    public function handle(FtpManager $ftp)
    {
        $current = $this->current ?? $ftp->currentDirectory();
        $ftp->changeDirectory($current);

        $path = rtrim($current, '/') . '/' . $this->file;

        $deleted = $ftp->rm($path);
        if ( ! $deleted) {
            throw new FailedToDeleteFileException('Failed to delete directory');
        }

        return $deleted;
    }
}
