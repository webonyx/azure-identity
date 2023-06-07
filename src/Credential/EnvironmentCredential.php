<?php

namespace Azure\Identity\Credential;

use Azure\Identity\EnvVar;
use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Required environment variables:
 * - `AZURE_TENANT_ID`: The Azure Active Directory tenant (directory) ID.
 * - `AZURE_CLIENT_ID`: The client (application) ID of an App Registration in the tenant.
 */
class EnvironmentCredential implements AzureCredentialInterface
{
    private $logger;

    private $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        $clientId = EnvVar::get('AZURE_CLIENT_ID');

        if (EnvVar::get('IDENTITY_ENDPOINT') && EnvVar::get('IDENTITY_HEADER')) {
            $client = new AppServiceCredential($clientId, $this->httpClient, $this->logger);
            return $client->getToken($scopes, $options);
        }

        $tenantId = EnvVar::get('AZURE_TENANT_ID');
        if (!$tenantId || !$clientId) {
            return null;
        }

        if (null !== $clientSecret = EnvVar::get('AZURE_CLIENT_SECRET')) {
            $client = new ClientSecretCredential($tenantId, $clientId, $clientSecret, $this->httpClient, $this->logger);
            return $client->getToken($scopes);
        }

        return null;
    }
}
