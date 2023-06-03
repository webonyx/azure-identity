<?php

namespace Azure\Identity\Credential;

use Azure\Identity\TokenInterface;
use Azure\Identity\Client\AadClient;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientSecretCredential implements TokenCredentialInterface
{
    private AadClient $client;

    public function __construct(string $tenantId, string $clientId, private string $clientSecret, ?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null)
    {
        $this->client = new AadClient($tenantId, $clientId, $httpClient, $logger);
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        return $this->client->fetchTokenByClientSecret($this->clientSecret, $scopes);
    }
}
