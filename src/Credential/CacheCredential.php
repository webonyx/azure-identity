<?php

namespace Azure\Identity\Credential;

use Azure\Identity\TokenInterface;
use Symfony\Contracts\Service\ResetInterface;

class CacheCredential implements AzureCredentialInterface, ResetInterface
{
    /**
     * @var (null|TokenInterface)[]
     */
    private array $cache = [];

    public function __construct(private AzureCredentialInterface $decorated)
    {

    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        $key = sha1(serialize([$scopes, $options]));
        if (!\array_key_exists($key, $this->cache) || (null !== $this->cache[$key] && $this->cache[$key]->isExpired())) {
            $this->cache[$key] = $this->decorated->getToken($scopes, $options);
        }

        return $this->cache[$key];
    }

    public function reset(): void
    {
        $this->cache = [];
    }
}
