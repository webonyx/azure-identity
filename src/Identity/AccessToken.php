<?php

namespace Azure\Identity;

class AccessToken implements AccessTokenInterface
{
    public function __construct(public string $accessToken, public int $expiresIn)
    {
    }

    public static function fromArray(array $token): AccessToken
    {
        return new self($token['access_token'], $token['expires_in']);
    }
}