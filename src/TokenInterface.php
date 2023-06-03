<?php

namespace Azure\Identity;

/**
 * @property-read string $accessToken
 */
interface TokenInterface
{
    public function isExpired(): bool;
    public function getExpireDate(): \DateTimeImmutable;
}
