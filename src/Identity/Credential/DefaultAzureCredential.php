<?php

namespace Azure\Identity\Credential;

class DefaultAzureCredential extends ChainedTokenCredential implements TokenCredentialInterface
{
    public function __construct(array $config = [])
    {
        parent::__construct([
            new EnvironmentCredential(),
            new WorkloadIdentityCredential($config),
        ]);
    }
}