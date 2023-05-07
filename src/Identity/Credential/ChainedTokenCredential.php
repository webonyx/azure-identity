<?php

namespace Azure\Identity\Credential;

use Azure\Identity\AccessTokenInterface;
use Azure\Identity\Exception\AuthenticationRequiredException;
use Azure\Identity\Exception\CredentialUnavailableException;

class ChainedTokenCredential implements TokenCredentialInterface
{
    /**
     * @var TokenCredentialInterface[]
     */
    private array $credentials;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }


    public function getToken(array $scopes, array $options = []): AccessTokenInterface
    {
        $token = null;

        foreach ($this->credentials as $credential) {
            try {
                $token = $credential->getToken($scopes, $options);
            } catch (CredentialUnavailableException|AuthenticationRequiredException $exception) {
                // eat it for now
            }
        }

        if (!$token) {
            throw new CredentialUnavailableException('Failed to retrieve a valid token');
        }

        return $token;
    }
}