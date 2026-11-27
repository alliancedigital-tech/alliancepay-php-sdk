<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Fiscalization\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationCallbackDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\CallbackPayloadResolver;

/**
 * Class FiscalizationCallbackHandler.
 */
class FiscalizationCallbackHandler
{
    private CallbackPayloadResolver $payloadResolver;

    public function __construct(
        private ?HttpClientInterface $httpClient = null,
        ?CallbackPayloadResolver $payloadResolver = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
        $this->payloadResolver = $payloadResolver ?? new CallbackPayloadResolver($this->httpClient);
    }

    /**
     * @param AuthorizationDTO $authorizationDTO
     * @param array|null $payload Already decoded payload; when null it is read from php://input.
     * @return FiscalizationCallbackDTO
     * @throws CallbackException
     */
    public function handle(AuthorizationDTO $authorizationDTO, ?array $payload = null): FiscalizationCallbackDTO
    {
        $payload = $this->payloadResolver->resolve($authorizationDTO, $payload);

        return FiscalizationCallbackDTO::fromArray($payload);
    }
}
