<?php

namespace Azure\Identity\HttpClient;

use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RetryStrategy extends GenericRetryStrategy
{
    public const DEFAULT_RETRY_STATUS_CODES = [0, 423, 425, 429, 500, 502, 503, 504, 507, 510];

    public function __construct(array $statusCodes = self::DEFAULT_RETRY_STATUS_CODES, int $delayMs = 1000, float $multiplier = 2.0, int $maxDelayMs = 0, float $jitter = 0.1)
    {
        parent::__construct($statusCodes, $delayMs, $multiplier, $maxDelayMs, $jitter);
    }

    public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
    {
        if (parent::shouldRetry($context, $responseContent, $exception)) {
            return true;
        }

        if (!\in_array($context->getStatusCode(), [400, 403], true)) {
            return false;
        }

        if (null === $responseContent) {
            return null; // null mean no decision taken and need to be called again with the body
        }

        return false;
    }
}
