<?php

namespace Azure\Identity\Credential;

use Azure\Identity\Client\AadClient;
use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientAssertionCredential implements TokenCredentialInterface
{
    private AadClient $client;

    public function __construct(public string $tenantId, public string $clientId, public \Closure $getAssertion, ?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null)
    {
        $this->client = new AadClient($tenantId, $clientId, $httpClient, $logger);
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        return $this->client->fetchTokenByClientAssertion($this->getAssertion, $scopes);
    }
}
