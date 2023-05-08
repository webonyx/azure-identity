<?php

namespace Azure\Identity\Exception;

class CredentialRequestErrorException extends \Exception
{
    public function __construct(public array $error, string $message)
    {
        parent::__construct($message);
    }
}
