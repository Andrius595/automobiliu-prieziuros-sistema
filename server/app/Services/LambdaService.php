<?php

namespace App\Services;

use Aws\Lambda\LambdaClient;
use Aws\Result;

class LambdaService
{

    public function __construct()
    {

    }

    public function invoke(array $params): Result
    {
        return $this->client->invoke($params);
    }
}
