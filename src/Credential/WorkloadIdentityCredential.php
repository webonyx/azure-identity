<?php

namespace Azure\Identity\Credential;

use Azure\Identity\EnvVar;
use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WorkloadIdentityCredential implements AzureCredentialInterface
{
    private $logger;

    private $httpClient;

    private ?string $tokenFilePath;

    public function __construct(array $options = [], ?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null)
    {
        $this->tokenFilePath = $options['tokenFilePath'] ?? EnvVar::get('AZURE_FEDERATED_TOKEN_FILE');
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    protected function getTokenFileContents(string $tokenFilePath): string
    {
        return file_get_contents($tokenFilePath);
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        if (!$this->tokenFilePath) {
            return null;
        }

        $tenantId = EnvVar::get('AZURE_TENANT_ID');
        $clientId = EnvVar::get('AZURE_CLIENT_ID');
        if (!$tenantId || !$clientId) {
            return null;
        }

        $client = new ClientAssertionCredential($tenantId, $clientId, fn() => $this->getTokenFileContents($this->tokenFilePath), $this->httpClient, $this->logger);
        return $client->getToken($scopes);
    }
}
