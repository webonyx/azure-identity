<?php

namespace Azure\Client;

class ClientAssertionClient extends BaseClient
{
    public function __construct(private string $tenantId, private string $clientId, public \Closure $getAssertion)
    {
        parent::__construct([
            'tenant_id' => $this->tenantId
        ]);
    }

    public function getToken(array $scopes, array $options = []): array
    {
        $params = [
            'client_id' => $this->clientId,
            'scope' => implode(',', [...$scopes]),
            'grant_type' => 'client_assertion',
        ];

        // clientAssertion
        $queryString = $this->buildQueryString($params);

        return $this->executePostToTokenEndpoint($this->getTokenEndpoint(), $queryString);
    }

    protected function getClientAssertion()
    {
        $getAssertion = $this->getAssertion;
        return [
            'assertion' => $getAssertion(),
            'assertionType' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
        ];
    }
}
