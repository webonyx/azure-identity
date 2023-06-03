<?php

namespace Azure\Identity;

/**
 * @property-read string $token
 * @property-read string $accessToken
 */
interface TokenInterface
{
    public function isExpired(): bool;
}
