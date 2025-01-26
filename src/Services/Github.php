<?php

namespace Codehubcare\LaravelDeployer\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Github
{
    protected $client;
    protected $baseUrl = 'https://api.github.com';
    protected $token;

    public function __construct()
    {
        $this->token = config('laravel-deployer.github_token');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/vnd.github.v3+json',
            ]
        ]);
    }

    public function getRepository($repository)
    {
        $response = $this->client->get("/repos/{$repository}");
        return json_decode($response->getBody(), true);
    }

    public function getBranches($repository)
    {
        $cacheKey = "github_branches_{$repository}";

        return Cache::remember($cacheKey, 3600, function () use ($repository) {
            $response = $this->client->get("/repos/{$repository}/branches");
            return json_decode($response->getBody(), true);
        });
    }

    public function getCommits($repository, $branch)
    {
        $response = $this->client->get("/repos/{$repository}/commits", [
            'query' => ['sha' => $branch]
        ]);
        return json_decode($response->getBody(), true);
    }

    public function getLatestCommit($repository, $branch)
    {
        $response = $this->client->get("/repos/{$repository}/commits/{$branch}");
        return json_decode($response->getBody(), true);
    }

    public function getFileContent($repository, $branch, $file)
    {
        $response = $this->client->get("/repos/{$repository}/contents/{$file}", [
            'query' => ['ref' => $branch]
        ]);
        $content = json_decode($response->getBody(), true);
        return base64_decode($content['content']);
    }

    protected function handleApiError($response)
    {
        if ($response->getStatusCode() >= 400) {
            throw new \Exception('GitHub API Error: ' . $response->getBody());
        }
    }
}
