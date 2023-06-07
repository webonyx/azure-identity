<?php

namespace Azure\Identity\Credential;

use Azure\Identity\TokenInterface;
use Psr\Cache\CacheException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SymfonyCacheCredential implements AzureCredentialInterface
{
    public function __construct(private AzureCredentialInterface $decorated, private CacheInterface $cache, private ?LoggerInterface $logger = null)
    {
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        $credential = $this->decorated;
        $closure = \Closure::fromCallable(static function (ItemInterface $item) use ($scopes, $options, $credential) {
            $token = $credential->getToken($scopes, $options);

            if (null !== $token && null !== $exp = $token->getExpireDate()) {
                $item->expiresAt($exp);
            } else {
                $item->expiresAfter(0);
            }

            return $token;
        });

        try {
            return $this->cache->get('Azure.Credentials.' . sha1(serialize([$scopes, $options, \get_class($this->decorated)])), $closure);
        } catch (CacheException $e) {
            $this->logger?->error('Failed to get Azure credentials from cache.', ['exception' => $e]);

            return $credential->getToken($scopes, $options);
        }
    }
}
