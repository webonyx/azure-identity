<?php

namespace Azure\Identity\Client;

use Azure\Identity\Token;
use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AadClient
{
    const DEFAULT_AZURE_AUTHORITY_HOST = 'https://login.microsoftonline.com/';

    const JWT_BEARER_ASSERTION = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';


    private $logger;

    private $httpClient;

    private int $timeout;

    public function __construct(private string $tenantId, private string $clientId, ?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null, int $timeout = 5)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->timeout = $timeout;
    }

    protected function getAuthority(): string
    {
        $authority = $_SERVER['AZURE_AUTHORITY_HOST'] ?? self::DEFAULT_AZURE_AUTHORITY_HOST;
        if (!str_starts_with($authority, 'http')) {
            $authority = "https://$authority";
        }

        return $authority;
    }

    protected function fetchToken(array $scopes, array $params): ?TokenInterface
    {
        $params = array_merge([
            'client_id' => $this->clientId,
            'scope' => implode(',', $scopes),
            'grant_type' => 'client_credentials',
        ], $params);

        $tokenEndpoint = $this->getTokenEndpoint();

        try {
            $response = $this->httpClient->request('POST', $tokenEndpoint, [
                'body' => $params,
                'timeout' => $this->timeout,
            ]);
            $result = $response->toArray();
        } catch (DecodingExceptionInterface $e) {
            $this->logger->info('Failed to decode token response.', ['exception' => $e]);

            return null;
        } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
            $this->logger->info('Failed to fetch token.', ['exception' => $e]);

            return null;
        }

        return Token::fromArray($result);
    }

    public function fetchTokenByClientSecret(string $secret, array $scopes): ?TokenInterface
    {
        return $this->fetchToken($scopes, ['client_secret' => $secret]);
    }

    public function fetchTokenByClientAssertion(\Closure $getClientAssertion, array $scopes): ?TokenInterface
    {
        return $this->fetchToken($scopes, [
            'client_assertion_type' => self::JWT_BEARER_ASSERTION,
            'client_assertion' => $getClientAssertion(),
        ]);
    }

    protected function getTokenEndpoint(): string
    {
        return implode('/', [rtrim($this->getAuthority(), '/'), $this->tenantId, 'oauth2/v2.0/token']);
    }
}
