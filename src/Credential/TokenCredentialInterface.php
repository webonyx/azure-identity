<?php

namespace Azure\Identity\Credential;

use Azure\Identity\TokenInterface;

interface TokenCredentialInterface
{
    public function getToken(array $scopes, array $options = []): ?TokenInterface;
}
