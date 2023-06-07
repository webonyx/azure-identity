<?php

namespace Azure\Identity\Credential;

use Azure\Identity\EnvVar;
use Azure\Identity\Exception\InvalidScopesException;
use Azure\Identity\Token;
use Azure\Identity\TokenInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppServiceCredential implements AzureCredentialInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(public ?string $clientId = null, ?HttpClientInterface $httpClient = null, private ?LoggerInterface $logger = null)
    {
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function getToken(array $scopes, array $options = []): ?TokenInterface
    {
        $url = EnvVar::get('IDENTITY_ENDPOINT');
        $secret = EnvVar::get('IDENTITY_HEADER');

        if (!$url || !$secret) {
            $this->logger->warning('App Service managed identity configuration not found in environment');

            return null;
        }

        $clientId = $this->clientId ?? EnvVar::get('AZURE_CLIENT_ID');
        $params = [
            'api-version' => '2019-08-01',
            'resource' => $this->scopesToResource($scopes),
        ];

        if ($clientId) {
            $params['client_id'] = $clientId;
        }

        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => array_merge($params, $options),
                'headers' => [
                    'X-IDENTITY-HEADER' => $secret
                ]
            ]);

            $result = $response->toArray();
        } catch (DecodingExceptionInterface $e) {
            $this->logger->info('Failed to decode token response.', ['exception' => $e]);

            return null;
        } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
            $this->logger->info('Failed to fetch token.', ['exception' => $e]);

            return null;
        }

        // App service return expires_on instead expires_in
        $result['expires_in'] = floor($result['expires_on'] - time());

        return Token::fromArray($result);
    }

    /**
     * Convert an AADv2 scope to an AADv1 resource
     * @param array $scopes
     * @return string
     */
    private function scopesToResource(array $scopes): string
    {
        if (count($scopes) !== 1) {
            throw new InvalidScopesException('AppServiceCredential requires exactly one scope per token request.');
        }

        $resource = $scopes[0];
        if (str_ends_with($resource, '/.default')) {
            $resource = substr($resource, 0, strlen('/.default') * -1);
        }

        return $resource;
    }
}
