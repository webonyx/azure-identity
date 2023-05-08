<?php

namespace Azure\Identity\Credential;

use Azure\Identity\AccessTokenInterface;
use Azure\Identity\Exception\CredentialUnavailableException;

interface TokenCredentialInterface
{
    /**
     * @throws CredentialUnavailableException
     */
    public function getToken(array $scopes, array $options = []): AccessTokenInterface;
}
