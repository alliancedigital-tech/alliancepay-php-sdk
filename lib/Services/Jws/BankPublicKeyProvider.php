<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;

/**
 * Class BankPublicKeyProvider.
 */
class BankPublicKeyProvider
{
    /**
     * @param HttpClientInterface $httpClient
     * @param string $baseUrl
     * @param string $kid
     * @return array
     * @throws EncryptionException
     */
    public static function fetch(HttpClientInterface $httpClient, string $baseUrl, string $kid): array
    {
        $response = $httpClient->request(
            HttpClientInterface::METHOD_POST,
            $baseUrl . HttpClientInterface::PATH_PUBLIC_KEY,
            [
                'json' => ['kid' => $kid],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );

        $jwk = $response['body'] ?? null;

        if (!is_array($jwk)
            || empty($jwk['kty'])
            || empty($jwk['crv'])
            || empty($jwk['x'])
            || empty($jwk['y'])
        ) {
            throw new EncryptionException(
                "Invalid public key response from bank for kid={$kid}"
            );
        }

        return $jwk;
    }
}
