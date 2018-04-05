<?php

namespace App\Command;

use App\Exception\FailedToDeleteDirectoryException;
use Framework\Ftp\FtpManager;

/**
 * Class DeleteDirectory
 *
 * @package App\Command
 */
class DeleteDirectory
{
    /**
     * @var string
     */
    private $directory;
    /**
     * @var string
     */
    private $current;

    /**
     * DeleteDirectory constructor.
     *
     * @param string $directory
     * @param string $current
     */
    public function __construct(string $directory, string $current = null)
    {

        $this->directory = $directory;
        $this->current   = $current;
    }

    /**
     * @param FtpManager $ftp
     */
    public function handle(FtpManager $ftp)
    {
        $current = $this->current ?? $ftp->currentDirectory();
        $ftp->changeDirectory($current);

        $files = dispatch(new GetFilesInDirectory($this->directory));
        if (count($files) > 0) {
            throw new FailedToDeleteDirectoryException('Cannot delete a directory unless it is empty.');
        }

        $deleted = $ftp->rmdir($this->directory);
        if ( ! $deleted) {
            throw new FailedToDeleteDirectoryException('Failed to delete directory');
        }
    }
}
