<?php

namespace Azure\Identity\Credential;

use Azure\Identity\AccessToken;
use Azure\Identity\AccessTokenInterface;
use Azure\Identity\Client\ClientCredentialClient;

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
