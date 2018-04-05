<?php

namespace App\Command;

use DateTimeZone;
use Framework\Ftp\FtpManager;

/**
 * Class GetFilesInDirectory
 *
 * @package App\Command
 */
class GetFilesInDirectory
{
    /**
     * @var null|string
     */
    private $directory;
    /**
     * @var string
     */
    private $timezone;

    /**
     * GetFilesInDirectory constructor.
     *
     * @param string|null $directory
     * @param string $timezone
     */
    public function __construct(string $directory = '.', string $timezone = 'UTC')
    {
        $this->directory = $directory;
        $this->timezone  = $timezone;
    }

    /**
     * @param FtpManager $ftp
     *
     * @return array
     */
    public function handle(FtpManager $ftp)
    {
        $timezone  = new DateTimeZone(config('ftp.timezone', 'UTC'));
        $ftimezone = new DateTimeZone($this->timezone);
        if (function_exists('ftp_mlsd')) {
            $files = $this->mlsd($ftp, $timezone, $ftimezone);
        } else {
            $files = $this->nlist($ftp, $timezone, $ftimezone);
        }

        return $this->sortFiles($files);
    }

    /**
     * @param FtpManager $ftp
     * @param DateTimeZone $timezone
     * @param DateTimeZone $ftimezone
     *
     * @return array
     */
    private function nlist(FtpManager $ftp, DateTimeZone $timezone, DateTimeZone $ftimezone)
    {
        $files           = $ftp->nlist($this->directory);
        if ( ! $files) {
            return [];
        }
        $files = array_map(function ($path) use ($ftp, $timezone, $ftimezone) {
            $filename      = preg_replace('/^' . preg_quote($this->directory, '/') . '\/?/', '', $path, 1);
            $modified      = $ftp->modified($path);
            $last_modified = null;
            if ($modified > 0) {
                $last_modified = $this->getFormattedDateFromFormat($modified, 'U', $timezone, $ftimezone);
            }
            $size = $ftp->size($path);
            $file = [
                'name'          => $filename,
                'modify'        => $modified > -1 ? $modified : null,
                'last_modified' => $last_modified,
                'size'          => $size > -1 ? $size : null,
                'fsize'         => $size > -1 ? bytesToSize($size) : null,
                'type'          => $size === -1 ? 'dir' : 'file',
            ];

            return $file;
        }, $files);

        return $files;
    }

    /**
     * @param FtpManager $ftp
     * @param DateTimeZone $timezone
     * @param DateTimeZone $ftimezone
     *
     * @return array
     */
    private function mlsd(FtpManager $ftp, DateTimeZone $timezone, DateTimeZone $ftimezone)
    {
        $files = $ftp->listFiles($this->directory);
        if ($files === false) {
            return [];
        }

        return array_map(function ($file) use ($timezone, $ftimezone) {
            $file['fsize']         = null;
            $file['last_modified'] = null;
            if ($file['type'] == 'file') {
                if (array_key_exists('size', $file)) {
                    $file['fsize'] = bytesToSize($file['size']);
                }
                if (array_key_exists('modify', $file)) {
                    $file['last_modified'] = $this->getFormattedDateFromFormat($file['modify'], 'YmdHis', $timezone, $ftimezone);
                }
            }

            return $file;
        }, array_filter($files, function ($file) {
            return in_array($file['type'], ['dir', 'file']);
        }));
    }

    /**
     * @param int|string $date
     * @param string $format
     * @param DateTimeZone $timezone
     * @param DateTimeZone $ftimezone
     *
     * @return null|string
     */
    public function getFormattedDateFromFormat($date, string $format, DateTimeZone $timezone, DateTimeZone $ftimezone)
    {
        if($format == 'U'){
            $modified = date_create_from_format($format, $date);
        } else {
            $modified = date_create_from_format($format, $date, $timezone);
        }

        $modified->setTimezone($ftimezone);
        if ($modified === false) {
            return null;
        }
        return $modified->format('m/d/Y h:i:s A');
    }

    private function sortFiles(array $files)
    {
        usort($files, function ($a, $b) {
            if ($a['type'] == $b['type']) {
                return strcmp($a['name'], $a['name']);
            }

            $as = ($a['type'] == 'dir') ? 0 : 1;
            $bs = ($b['type'] == 'dir') ? 0 : 1;

            return $as <=> $bs;
        });

        return $files;
    }
}
