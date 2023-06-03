<?php

namespace Azure\Identity\Credential;

use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\ResetInterface;

class ChainTokenCredential implements TokenCredentialInterface, ResetInterface
{
    /**
     * @var TokenCredentialInterface[]
     */
    private array $credentials;

    /**
     * @var TokenCredentialInterface[]
     */
    private array $lastSuccessfulCredentials = [];

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public static function createDefaultChain(array $config, ?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null): TokenCredentialInterface
    {
        return new ChainTokenCredential([
            new EnvironmentCredential($httpClient, $logger),
            new WorkloadIdentityCredential($config, $httpClient, $logger),
        ]);
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        $key = sha1(serialize([$scopes, $options]));
        if (\array_key_exists($key, $this->lastSuccessfulCredentials)) {
            if (null === $credential = $this->lastSuccessfulCredentials[$key]) {
                return null;
            }

            return $credential->getToken($scopes, $options);
        }

        foreach ($this->credentials as $credential) {
            if (null !== $token = $credential->getToken($scopes, $options)) {
                $this->lastSuccessfulCredentials[$key] = $credential;
                return $token;
            }
        }

        $this->lastSuccessfulCredentials[$key] = null;

        return null;
    }

    public function reset(): void
    {
        $this->lastSuccessfulCredentials = [];
    }
}
