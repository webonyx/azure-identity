<?php

namespace Azure\Identity\Credential;

use Azure\Identity\AccessTokenInterface;
use Azure\Identity\Exception\CredentialUnavailableException;

/**
 * Required environment variables:
 * - `AZURE_TENANT_ID`: The Azure Active Directory tenant (directory) ID.
 * - `AZURE_CLIENT_ID`: The client (application) ID of an App Registration in the tenant.
 *
 * @todo Support ClientCertificateCredential
 */
class EnvironmentCredential implements TokenCredentialInterface
{
    private ?ClientSecretCredential $credential = null;

    public function __construct()
    {
        $tenantId = $_SERVER['AZURE_TENANT_ID'] ?? getenv('AZURE_TENANT_ID');
        $clientId = $_SERVER['AZURE_CLIENT_ID'] ?? getenv('AZURE_CLIENT_ID');
        $clientSecret = $_SERVER['AZURE_CLIENT_SECRET'] ?? getenv('AZURE_CLIENT_SECRET');

        if ($tenantId && $clientId && $clientSecret) {
            $this->credential = new ClientSecretCredential($tenantId, $clientId, $clientSecret);
        }
    }

    public function getToken(array $scopes, array $options = []): AccessTokenInterface
    {
        if (!$this->credential) {
            throw new CredentialUnavailableException('EnvironmentCredential is unavailable. No underlying credential could be used.');
        }

        return $this->credential->getToken($scopes, $options);
    }
}