<?php

namespace Azure\Client;

class IdentityClient
{
    public string $authorityHost = 'https://login.microsoftonline.com';

    public function __construct(array $options = [])
    {
        $authorityHost = $options['authorityHost'] ?? $_SERVER['AZURE_AUTHORITY_HOST'] ?? getenv('AZURE_AUTHORITY_HOST');
        if ($authorityHost) {
            $this->authorityHost = $authorityHost;
        }

        $options = array_merge([
            'requestContentType' => 'application/json; charset=utf-8',
            'retryOptions' => ['maxRetries' => 3],
            'baseUri' => $this->authorityHost
        ], $options);
    }
}