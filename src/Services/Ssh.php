<?php

namespace Codehubcare\LaravelDeployer\Services;

use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;


class Ssh
{
    private $ssh;
    private $sftp;
    private $host;
    private $username;
    private $password;
    private $port;

    public function __construct($host = null, $username = null, $password = null, $port = 22)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }


    /**
     * Connect to server
     *
     * @return void
     */
    public function connect()
    {
        try {
            $this->ssh = new SSH2($this->host, $this->port);
            $this->sftp = new SFTP($this->host, $this->port);

            if (!$this->ssh->login($this->username, $this->password) || !$this->sftp->login($this->username, $this->password)) {
                throw new \Exception('Login failed');
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect from server
     *
     * @return void
     */
    public function disconnect()
    {
        $this->ssh = null;
        $this->sftp = null;
        return true;
    }

    /**
     * Execute a command on the remote server
     *
     * @param string $command
     * @return string
     */
    public function execute($command)
    {
        if (!$this->ssh) {
            throw new \Exception('Not connected');
        }
        return $this->ssh->exec($command);
    }

    /**
     *  Uploads a file to the remote server.
     *
     * @param string $file
     * @param string $destination
     * @return void
     */
    public function upload($file, $destination)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->put($destination, $file, SFTP::SOURCE_LOCAL_FILE);
    }

    /**
     * Uploads a directory recursively to the remote server.
     *
     * @param string $directory The local directory path to upload.
     * @param string $destination The remote directory path to upload to.
     * @return void
     */
    public function uploadDirectory($directory, $destination)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
                
        // Ensure trailing slash consistency
        $directory = rtrim($directory, '/');
        $destination = rtrim($destination, '/');
        
        // Verify and create destination directory
        if (!$this->sftp->is_dir($destination)) {
            if (!$this->sftp->mkdir($destination, 0777, true)) {
                throw new \Exception("Failed to create directory: $destination");
            }
        }

        $items = scandir($directory);
        if ($items === false) {
            throw new \Exception("Failed to read directory: $directory");
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $localPath = $directory . '/' . $item;
            $remotePath = $destination . '/' . $item;

            if (is_dir($localPath)) {
                $this->uploadDirectory($localPath, $remotePath);
            } else {
                $this->sftp->put($remotePath, $localPath, SFTP::SOURCE_LOCAL_FILE);
            }
        }

        return true;
    }

    /**
     * Downloads a file from the remote server.
     *
     * @param string $file
     * @param string $destination
     * @return void
     */
    public function download($file, $destination)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->get($file, $destination);
    }

    /**
     * Downloads a directory from the remote server.
     *
     * @param string $directory The remote directory path to download.
     * @param string $destination The local directory path to download to.
     * @return void
     */
    public function downloadDirectory($directory, $destination)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }

        // Ensure the destination directory exists
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        // Get the list of files and directories in the specified directory
        $items = $this->sftp->nlist($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $remotePath = $directory . '/' . $item;
            $localPath = $destination . '/' . $item;

            if ($this->sftp->is_dir($remotePath)) {
                // Recursively download subdirectory
                $this->downloadDirectory($remotePath, $localPath);
            } else {
                // Download file
                $this->sftp->get($remotePath, $localPath);
            }
        }

        return true;
    }

    /**
     * Deletes a file from the remote server.
     *
     * @param string $file The remote file path to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->delete($file);
    }

    /**
     * Lists the contents of a directory on the remote server.
     *
     * @param string $directory The remote directory path to list. Defaults to '.'.
     * @return array An array of file and directory names.
     */
    public function list($directory = '.')
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->nlist($directory);
    }

    /**
     * Gets the file stats from the remote server.
     *
     * @param string $file The remote file path to get stats for.
     * @return array An array of file stats.
     */
    public function getFile($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->stat($file);
    }

    /**
     * Gets the stats of a directory on the remote server.
     *
     * @param string $directory The remote directory path to get stats for.
     * @return array An array of directory stats.
     */
    public function getDirectory($directory)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->stat($directory);
    }

    /**
     * Gets the content of a file from the remote server.
     *
     * @param string $file The remote file path to get content for.
     * @return string The file content.
     */
    public function getFileContent($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->get($file);
    }

    /**
     * Lists the contents of a directory on the remote server.
     *
     * @param string $directory The remote directory path to list. Defaults to '.'.
     * @return array An array of file and directory names.
     */
    public function getDirectoryContent($directory = '.')
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->rawlist($directory);
    }

    /**
     * Gets the size of a file from the remote server.
     *
     * @param string $file The remote file path to get size for.
     * @return int|null The file size in bytes, or null if not found.
     */
    public function getFileSize($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['size'] ?? null;
    }

    /**
     * Gets the permissions of a file from the remote server.
     *
     * @param string $file The remote file path to get permissions for.
     * @return int|null The file permissions, or null if not found.
     */
    public function getFilePermissions($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['permissions'] ?? null;
    }

    /**
     * Gets the owner of a file from the remote server.
     *
     * @param string $file The remote file path to get owner for.
     * @return int|null The file owner, or null if not found.
     */
    public function getFileOwner($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['uid'] ?? null;
    }

    /**
     * Gets the group of a file from the remote server.
     *
     * @param string $file The remote file path to get group for.
     * @return int|null The file group, or null if not found.
     */
    public function getFileGroup($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['gid'] ?? null;
    }

    /**
     * Gets the last modified time of a file from the remote server.
     *
     * @param string $file The remote file path to get last modified time for.
     * @return int|null The file last modified time in Unix timestamp, or null if not found.
     */
    public function getFileLastModified($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['mtime'] ?? null;
    }
}
