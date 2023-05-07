<?php

namespace Azure\Identity\Credential;

use Azure\Identity\AccessTokenInterface;

class ClientAssertionCredential implements TokenCredentialInterface
{
    public function __construct(public string $tenantId, public string $clientId, public \Closure $getAssertion)
    {
    }

    public function getToken(array $scopes, array $options = []): AccessTokenInterface
    {
        // TODO: Implement getToken() method.
    }
}