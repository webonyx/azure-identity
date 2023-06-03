<?php

namespace Azure\Identity;

class Token implements TokenInterface
{
    private const EXPIRATION_DRIFT = 30;

    private $expireDate;

    public function __construct(public string $accessToken, public int $expiresIn)
    {
        $this->expireDate = (new \DateTimeImmutable())
            ->add(new \DateInterval(sprintf('PT%dS', $expiresIn)))
            ->sub(new \DateInterval(sprintf('PT%dS', self::EXPIRATION_DRIFT)));
    }

    public function isExpired(): bool
    {
        return null !== $this->expireDate && new \DateTimeImmutable() >= $this->expireDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireDate(): \DateTimeImmutable
    {
        return $this->expireDate;
    }

    public static function fromArray(array $token): TokenInterface
    {
        return new self($token['access_token'], $token['expires_in']);
    }
}
