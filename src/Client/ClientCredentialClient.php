<?php

namespace Azure\Identity\Client;

class ClientCredentialClient extends BaseClient
{
    public function __construct(private string $tenantId, private string $clientId, private string $clientSecret)
    {
        parent::__construct([
            'tenant_id' => $this->tenantId
        ]);
    }

    public function getOauth2Parameters(array $scopes, array $options = []): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => implode(',', $scopes),
            'grant_type' => 'client_credentials',
        ];
    }
}
