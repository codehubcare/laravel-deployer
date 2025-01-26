<?php

namespace Codehubcare\LaravelDeployer\Services;

class Ssh
{
    public function __construct() {}

    public function connect()
    {
        return 'Connected to SSH';
    }

    public function disconnect()
    {
        return 'Disconnected from SSH';
    }

    public function execute($command)
    {
        return 'Executing command: ' . $command;
    }

    public function upload($file, $destination)
    {
        return 'Uploading file: ' . $file . ' to destination: ' . $destination;
    }

    public function download($file, $destination)
    {
        return 'Downloading file: ' . $file . ' to destination: ' . $destination;
    }

    public function delete($file)
    {
        return 'Deleting file: ' . $file;
    }

    public function list()
    {
        return 'Listing files';
    }

    public function getFile($file)
    {
        return 'Getting file: ' . $file;
    }

    public function getDirectory($directory)
    {
        return 'Getting directory: ' . $directory;
    }

    public function getFileContent($file)
    {
        return 'Getting file content: ' . $file;
    }

    public function getDirectoryContent($directory)
    {
        return 'Getting directory content: ' . $directory;
    }

    public function getFileSize($file)
    {
        return 'Getting file size: ' . $file;
    }

    public function getFilePermissions($file)
    {
        return 'Getting file permissions: ' . $file;
    }

    public function getFileOwner($file)
    {
        return 'Getting file owner: ' . $file;
    }

    public function getFileGroup($file)
    {
        return 'Getting file group: ' . $file;
    }

    public function getFileLastModified($file)
    {
        return 'Getting file last modified: ' . $file;
    }
}
