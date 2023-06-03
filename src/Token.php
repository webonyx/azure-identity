<?php

namespace Azure\Identity;

class Token implements TokenInterface
{
    private const EXPIRATION_DRIFT = 30;

    private $expireDate;

    /**
     * @throws \Exception
     */
    public function __construct(public string $token, public int $expiresIn)
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
     * @throws \Exception
     */
    public static function fromArray(array $token): TokenInterface
    {
        return new self($token['access_token'], $token['expires_in']);
    }
}
