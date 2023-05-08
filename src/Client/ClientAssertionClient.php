<?php

namespace Azure\Client;

class ClientAssertionClient extends BaseClient
{
    const JWT_BEARER_ASSERTION = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';

    public function __construct(private string $tenantId, private string $clientId, public \Closure $getAssertion, array $options = [])
    {
        parent::__construct(array_merge($options, ['tenant_id' => $this->tenantId]));
    }

    public function getToken(array $scopes, array $options = []): array
    {
        $params = [
            'client_id' => $this->clientId,
            'scope' => implode(',', [...$scopes]),
            'client_assertion_type' => self::JWT_BEARER_ASSERTION,
            'client_assertion' => ($this->getAssertion)(),
            'grant_type' => 'client_credentials',
        ];

        // clientAssertion
        $queryString = $this->buildQueryString($params);

        return $this->executePostToTokenEndpoint($this->getTokenEndpoint(), $queryString);
    }


}
