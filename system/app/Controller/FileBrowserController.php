<?php

namespace App\Controller;

use App\Command\CreateDirectory;
use App\Command\DeleteDirectory;
use App\Command\DeleteFile;
use App\Command\GetFileDownload;
use App\Command\GetFilesInDirectory;
use App\Command\UploadFile;
use App\Exception\FailedToCreateDirectoryException;
use App\Exception\FailedToDeleteDirectoryException;
use App\Exception\FailedToDeleteFileException;
use App\Exception\FailedToDownloadFileException;
use App\Exception\FailedToUploadFileException;
use Framework\Ftp\FtpManager;

/**
 * Class HomeController
 *
 * @package App\Controller
 */
class FileBrowserController
{
    /**
     * @var FtpManager
     */
    private $ftp;

    /**
     * FileBrowserController constructor.
     *
     * @param FtpManager $ftp
     */
    public function __construct(FtpManager $ftp)
    {
        $this->ftp = $ftp;
    }

    public function home()
    {
        $directory = $this->ftp->currentDirectory();
        $maxsize   = return_bytes(ini_get('upload_max_filesize'));
        $message   = session()->getFlashBag()->get('download');
        $message   = array_shift($message);

        return view('home', compact('directory', 'maxsize', 'message'));
    }

    /**
     * Returns list of files and folders in current directory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $directory = request('directory', $this->ftp->currentDirectory());
        $timezone = request()->header('X-Timezone', config('app.timezone', 'UTC'));
        $files     = dispatch(new GetFilesInDirectory($directory, $timezone));

        return response()->json(['directory' => $directory, 'files' => $files]);
    }

    /**
     * Move up a directory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function up()
    {
        $current = request('current', $this->ftp->currentDirectory());
        $this->ftp->changeDirectory($current);
        $changed = $this->ftp->upDirectory();
        if ( ! $changed) {
            return response()->json(['success' => false, 'message' => 'Could not change directory']);
        }
        $directory = $this->ftp->currentDirectory();
        $files     = dispatch(new GetFilesInDirectory());

        return response()->json(['success' => true, 'directory' => $directory, 'files' => $files]);
    }

    /**
     * Change directory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chdir()
    {
        $current   = request('current', $this->ftp->currentDirectory());
        $directory = rtrim('/' . request('directory'), '/');

        $changed = $this->ftp->changeDirectory($current . $directory);
        if ( ! $changed) {
            return response()->json(['success' => false, 'message' => 'Could not change directory']);
        }
        $directory = $this->ftp->currentDirectory();
        $files     = dispatch(new GetFilesInDirectory());

        return response()->json(['success' => true, 'directory' => $directory, 'files' => $files]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function mkdir()
    {
        $current   = request('current', $this->ftp->currentDirectory());
        $directory = request('directory');

        try {
            $directory = dispatch(new CreateDirectory($directory, $current));
        } catch (FailedToCreateDirectoryException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $files = dispatch(new GetFilesInDirectory());

        return response()->json(['success' => true, 'directory' => $directory, 'files' => $files]);
    }

    public function upload()
    {
        $directory = request('directory', $this->ftp->currentDirectory());
        $file      = request()->file('file');

        try {
            set_time_limit(0);
            dispatch(new UploadFile($file, $directory));
        } catch (FailedToUploadFileException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $directory = $this->ftp->currentDirectory();
        $files     = dispatch(new GetFilesInDirectory());

        return response()->json(['success' => true, 'directory' => $directory, 'files' => $files]);
    }

    /**
     * Downloads a file
     *
     * @param string $path
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $path)
    {
        try {
            $download = dispatch(new GetFileDownload($path));
        } catch (FailedToDownloadFileException $e) {
            session()->getFlashBag()->add('download', $e->getMessage());

            return redirect()->back();
        }
        set_time_limit(0);
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        return response()->streamDownload(function () use ($download) {
            session_write_close();
            $chunk = 10 * 1024 * 1024; // bytes per chunk (10 MB
            while ( ! feof($download['stream'])) {
                echo fread($download['stream'], $chunk);

                flush();
            }

            fclose($download['stream']);
        }, $download['filename'], [
            'Content-Description' => 'File Transfer',
            'Content-Type'        => 'application/octet-stream',
            'Expires'             => 0,
            'Cache-Control'       => 'must-revalidate',
            'Pragma'              => 'public',
            'Content-length'      => $download['size'],
            'Last-Modified'       => $download['modified'],
        ]);
    }

    public function rm()
    {
        $directory = request('directory', $this->ftp->currentDirectory());
        $file      = request('file');
        if ( ! $file) {
            return response()->json(['success' => false, 'message' => 'No file provided']);
        }

        try {
            dispatch(new DeleteFile($file, $directory));
        } catch (FailedToDeleteFileException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $directory = $this->ftp->currentDirectory();
        $files     = dispatch(new GetFilesInDirectory());

        return response()->json(['success' => true, 'directory' => $directory, 'files' => $files]);
    }

    public function rmdir()
    {
        $current   = request('current', $this->ftp->currentDirectory());
        $directory = request('directory');
        if ( ! $directory) {
            return response()->json(['success' => false, 'message' => 'No directory provided']);
        }

        try {
            dispatch(new DeleteDirectory($directory, $current));
        } catch (FailedToDeleteDirectoryException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $directory = $this->ftp->currentDirectory();
        $files     = dispatch(new GetFilesInDirectory());

        return response()->json(['success' => true, 'directory' => $directory, 'files' => $files]);
    }
}
