<?php

namespace Azure\Identity\Credential;

use Azure\Client\ClientCredentialClient;
use Azure\Identity\AccessToken;
use Azure\Identity\AccessTokenInterface;

class ClientSecretCredential implements TokenCredentialInterface
{
    private ClientCredentialClient $client;

    public function __construct(string $tenantId, string $clientId, string $clientSecret)
    {
        $this->client = new ClientCredentialClient($tenantId, $clientId, $clientSecret);
    }

    public function getToken(array $scopes, array $options = []): AccessTokenInterface
    {
        $token = $this->client->getToken($scopes, $options);

        return AccessToken::fromArray($token);
    }
}