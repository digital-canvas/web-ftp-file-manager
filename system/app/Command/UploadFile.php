<?php

namespace App\Command;

use App\Exception\FailedToUploadFileException;
use Framework\Ftp\FtpManager;
use Illuminate\Http\UploadedFile;

/**
 * Class UploadFile
 *
 * @package App\Command
 */
class UploadFile
{
    /**
     * @var UploadedFile
     */
    private $file;
    /**
     * @var string
     */
    private $directory;

    /**
     * UploadFile constructor.
     *
     * @param UploadedFile $file
     * @param string $directory
     */
    public function __construct(UploadedFile $file, $directory = '.')
    {
        $this->file      = $file;
        $this->directory = $directory;
    }

    /**
     * @param FtpManager $ftp
     */
    public function handle(FtpManager $ftp)
    {
        if ( ! $this->file->isValid()) {
            throw new FailedToUploadFileException($this->file->getErrorMessage());
        }
        $stream = fopen($this->file->getRealPath(), 'r');
        if ($stream === false) {
            throw new FailedToUploadFileException('Could not read uploaded file');
        }
        $ftp->changeDirectory($this->directory);
        $uploaded = $ftp->upload($stream, $this->file->getClientOriginalName());
        fclose($stream);

        if ( ! $uploaded) {
            throw new FailedToUploadFileException('Failed to upload file');
        }
    }
}
