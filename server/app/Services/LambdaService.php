<?php

namespace App\Services;

use Aws\Lambda\LambdaClient;
use Aws\Result;

class LambdaService
{
    private LambdaClient $client;

    public function __construct()
    {
        $this->client = new LambdaClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function invoke(array $params): Result
    {
        return $this->client->invoke($params);
    }
}
