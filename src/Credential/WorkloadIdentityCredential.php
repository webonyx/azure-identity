<?php

namespace Azure\Identity\Credential;

use Azure\Identity\AccessTokenInterface;
use Azure\Identity\Exception\CredentialUnavailableException;

class WorkloadIdentityCredential implements TokenCredentialInterface
{
    public ?ClientAssertionCredential $clientAssertionCredential = null;

    public function __construct(array $options = [])
    {
        $tenantId = $options['tenantId'] ?? $_SERVER['AZURE_TENANT_ID'] ?? getenv('AZURE_TENANT_ID');
        $clientId = $options['clientId'] ?? $_SERVER['AZURE_CLIENT_ID'] ?? getenv('AZURE_CLIENT_ID');
        $tokenFilePath = $options['tokenFilePath'] ?? $_SERVER['AZURE_FEDERATED_TOKEN_FILE'] ?? getenv('AZURE_FEDERATED_TOKEN_FILE');

        if ($tenantId && $clientId && $tokenFilePath) {
            $this->clientAssertionCredential = new ClientAssertionCredential($tenantId, $clientId, fn() => $this->getTokenFileContents($tokenFilePath));
        }
    }

    protected function getTokenFileContents(string $tokenFilePath): string
    {
        return file_get_contents($tokenFilePath);
    }

    public function getToken(array $scopes, array $options = []): AccessTokenInterface
    {
        if (!$this->clientAssertionCredential) {
            throw new CredentialUnavailableException(' WorkloadIdentityCredential: is unavailable. tenantId, clientId, and federatedTokenFilePath are required parameters. 
      In DefaultAzureCredential and ManagedIdentityCredential, these can be provided as environment variables - 
      "AZURE_TENANT_ID",
      "AZURE_CLIENT_ID",
      "AZURE_FEDERATED_TOKEN_FILE". See the troubleshooting guide for more information: https://aka.ms/azsdk/js/identity/workloadidentitycredential/troubleshoot');
        }

        return $this->clientAssertionCredential->getToken($scopes, $options);
    }
}
