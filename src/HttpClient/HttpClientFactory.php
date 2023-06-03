<?php

namespace Azure\Identity\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientFactory
{
    public static function createRetryableClient(HttpClientInterface $httpClient = null, LoggerInterface $logger = null): HttpClientInterface
    {
        if (null === $httpClient) {
            $httpClient = HttpClient::create();
        }

        if (class_exists(RetryableHttpClient::class)) {
            /** @psalm-suppress MissingDependency */
            $httpClient = new RetryableHttpClient(
                $httpClient,
                new RetryStrategy(),
                3,
                $logger
            );
        }

        return $httpClient;
    }
}
