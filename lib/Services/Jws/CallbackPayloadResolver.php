<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Jws;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;

/**
 * Class CallbackPayloadResolver.
 */
class CallbackPayloadResolver
{
    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param AuthorizationDTO $authorizationDTO
     * @param array|null $payload
     * @return array
     * @throws CallbackException
     */
    public function resolve(AuthorizationDTO $authorizationDTO, ?array $payload = null): array
    {
        if (!empty($payload)) {
            return $payload;
        }

        $rawBody = trim(file_get_contents('php://input'));

        // Try plain JSON FIRST, same precedence SimpleJWT\Helper::detect() itself uses
        // internally. A compact JWS never successfully json_decode()s (it isn't JSON), so
        // this can never misclassify a real token — but checking the "looks like a token"
        // heuristic (no spaces, exactly 2 dots) BEFORE this would misroute a plain JSON body
        // into JWS verification whenever it happens to contain exactly two literal dots
        // (e.g. two decimal fields such as coinAmount/conversionRate).
        $payload = json_decode($rawBody, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            // genuinely plain JSON — never attempt JWS verification on it
        } else {
            $isToken = $rawBody !== '' && !str_contains($rawBody, ' ') && substr_count($rawBody, '.') === 2;

            if ($isToken) {
                try {
                    $header = JwsVerificationService::deserialiseHeader($rawBody);
                    $publicKeyJwk = BankPublicKeyProvider::fetch(
                        $this->httpClient,
                        $authorizationDTO->getBaseUrl(),
                        $header['kid']
                    );
                    $payload = JwsVerificationService::verify($rawBody, $publicKeyJwk, $header['alg']);
                } catch (EncryptionException $e) {
                    throw new CallbackException('JWS callback verification failed: ' . $e->getMessage());
                }
            } else {
                throw new CallbackException("Invalid JSON payload: " . json_last_error_msg());
            }
        }

        if (empty($payload)) {
            throw new CallbackException('Empty callback payload');
        }

        return $payload;
    }
}
