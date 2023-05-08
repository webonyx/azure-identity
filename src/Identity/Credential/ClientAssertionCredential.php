<?php

namespace Azure\Identity\Credential;

use Azure\Client\ClientAssertionClient;
use Azure\Identity\AccessToken;
use Azure\Identity\AccessTokenInterface;
use Azure\Identity\Exception\CredentialRequestErrorException;

class ClientAssertionCredential implements TokenCredentialInterface
{
    private ClientAssertionClient $aadClient;
    public function __construct(public string $tenantId, public string $clientId, public \Closure $getAssertion)
    {
        $this->aadClient = new ClientAssertionClient($tenantId, $clientId, $getAssertion);
    }

    public function getToken(array $scopes, array $options = []): AccessTokenInterface
    {
        $result = $this->aadClient->getToken($scopes, $options);
        if (isset($result['error'])) {
            $message = sprintf('ClientAssertionCredential: %s', $result['error_description'] ?? $result['error']);
            throw new CredentialRequestErrorException($result, $message);
        }

        return AccessToken::fromArray($result);
    }
}
