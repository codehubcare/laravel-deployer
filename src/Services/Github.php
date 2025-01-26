<?php

namespace Codehubcare\LaravelDeployer\Services;

class Github
{
    public function __construct() {}

    public function getRepository($repository)
    {
        return 'Getting repository: ' . $repository;
    }

    public function getBranches($repository)
    {
        return 'Getting branches: ' . $repository;
    }

    public function getCommits($repository, $branch)
    {
        return 'Getting commits: ' . $repository . ' on branch: ' . $branch;
    }

    public function getLatestCommit($repository, $branch)
    {
        return 'Getting latest commit: ' . $repository . ' on branch: ' . $branch;
    }

    public function getFileContent($repository, $branch, $file)
    {
        return 'Getting file content: ' . $repository . ' on branch: ' . $branch . ' file: ' . $file;
    }
}
