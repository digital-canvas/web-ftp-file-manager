<?php

namespace App\Command;

use App\Exception\FailedToCreateDirectoryException;
use Framework\Ftp\FtpManager;

/**
 * Class CreateDirectory
 *
 * @package App\Command
 */
class CreateDirectory
{
    /**
     * @var string
     */
    private $directory;
    /**
     * @var null|string
     */
    private $current;

    /**
     * CreateDirectory constructor.
     *
     * @param string $directory
     * @param string|null $current
     */
    public function __construct(string $directory, string $current = null)
    {
        $this->directory = $directory;
        $this->current   = $current;
    }

    /**
     * @param FtpManager $ftp
     *
     * @return string
     */
    public function handle(FtpManager $ftp)
    {
        if ($this->current) {
            $ftp->changeDirectory($this->current);
        }
        $created = $ftp->mkdir($this->directory);
        if ( ! $created) {
            throw new FailedToCreateDirectoryException("Failed to create directory {$this->directory}");
        }
        $ftp->changeDirectory($this->directory);

        return $ftp->currentDirectory();
    }
}
