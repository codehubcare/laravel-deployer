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

    public function connect()
    {
        try {
            $this->ssh = new SSH2($this->host, $this->port);
            $this->sftp = new SFTP($this->host, $this->port);

            if (
                !$this->ssh->login($this->username, $this->password) ||
                !$this->sftp->login($this->username, $this->password)
            ) {
                throw new \Exception('Login failed');
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Connection failed: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        $this->ssh = null;
        $this->sftp = null;
        return true;
    }

    public function execute($command)
    {
        if (!$this->ssh) {
            throw new \Exception('Not connected');
        }
        return $this->ssh->exec($command);
    }

    public function upload($file, $destination)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->put($destination, $file, SFTP::SOURCE_LOCAL_FILE);
    }

    public function download($file, $destination)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->get($file, $destination);
    }

    public function delete($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->delete($file);
    }

    public function list($directory = '.')
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->nlist($directory);
    }

    public function getFile($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->stat($file);
    }

    public function getDirectory($directory)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->stat($directory);
    }

    public function getFileContent($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->get($file);
    }

    public function getDirectoryContent($directory)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        return $this->sftp->rawlist($directory);
    }

    public function getFileSize($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['size'] ?? null;
    }

    public function getFilePermissions($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['permissions'] ?? null;
    }

    public function getFileOwner($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['uid'] ?? null;
    }

    public function getFileGroup($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['gid'] ?? null;
    }

    public function getFileLastModified($file)
    {
        if (!$this->sftp) {
            throw new \Exception('Not connected');
        }
        $stat = $this->sftp->stat($file);
        return $stat['mtime'] ?? null;
    }
}
