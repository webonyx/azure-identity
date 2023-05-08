<?php

namespace Azure\Identity\Client;

use Http\Discovery\Psr18Client;
use Nyholm\Psr7\Stream;

abstract class BaseClient
{
    const DEFAULT_AZURE_AUTHORITY_HOST = 'https://login.microsoftonline.com/';

    protected Psr18Client $httpClient;

    public function __construct(protected array $options = [])
    {
        $this->httpClient = new Psr18Client();
    }

    protected function getTenantId(): string
    {
        return $this->options['tenant_id'] ?? 'common';
    }

    protected function getAuthority(): string
    {
        $authority = $this->options['authority'] ?? $_SERVER['AZURE_AUTHORITY_HOST'] ?? self::DEFAULT_AZURE_AUTHORITY_HOST;
        if (!str_starts_with($authority, 'http')) {
            $authority = "https://$authority";
        }

        return $authority;
    }

    public function getToken(array $scopes, array $options = []): array
    {
        $params = $this->getOauth2Parameters($scopes, $options);
        $queryString = $this->buildQueryString($params);

        return $this->executePostToTokenEndpoint($this->getTokenEndpoint(), $queryString);
    }

    abstract public function getOauth2Parameters(array $scopes, array $options = []): array;

    protected function getTokenEndpoint(): string
    {
        return implode('/', [rtrim($this->getAuthority(), '/'), $this->getTenantId(), 'oauth2/v2.0/token']);
    }

    protected function executePostToTokenEndpoint(string $tokenEndpoint, string $queryString, array $headers = []): array
    {
        $headers = array_merge([
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
        ], $headers);


        $request = $this->httpClient->createRequest('POST', $tokenEndpoint, $headers, $queryString);
        $request = $request->withBody(Stream::create($queryString));

        foreach ($headers as $name => $header) {
            $request = $request->withHeader($name, $header);
        }

        $response = $this->httpClient->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function buildQueryString(array $params): string
    {
        return http_build_query($params, '', '&', \PHP_QUERY_RFC3986);
    }
}
