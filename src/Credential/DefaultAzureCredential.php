<?php

namespace Azure\Identity\Credential;

use Azure\Identity\HttpClient\HttpClientFactory;
use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultAzureCredential implements AzureCredentialInterface
{
    private AzureCredentialInterface $cacheable;

    public function __construct(array $config = [], ?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null)
    {
        $logger = $logger ?? new NullLogger();
        if (null === $httpClient) {
            $httpClient = HttpClientFactory::createRetryableClient(null, $logger);
        }

        $this->cacheable = new CacheCredential(
            ChainTokenCredential::createDefaultChain($config, $httpClient, $logger)
        );
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        return $this->cacheable->getToken($scopes, $options);
    }
}
