<?php

namespace Azure\Client;

class ClientCredentialClient extends BaseClient
{
    public function __construct(private string $tenantId, private string $clientId, private string $clientSecret)
    {
        parent::__construct([
            'tenant_id' => $this->tenantId
        ]);
    }

    public function getToken(array $scopes, array $options = []): array
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => implode(',', [...$scopes]),
            'grant_type' => 'client_credentials',
        ];

        $queryString = $this->buildQueryString($params);

        return $this->executePostToTokenEndpoint($this->getTokenEndpoint(), $queryString);
    }
}