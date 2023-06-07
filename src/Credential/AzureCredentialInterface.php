<?php

namespace Azure\Identity\Credential;

use Azure\Identity\TokenInterface;

interface AzureCredentialInterface
{
    public function getToken(array $scopes, array $options = []): ?TokenInterface;
}
