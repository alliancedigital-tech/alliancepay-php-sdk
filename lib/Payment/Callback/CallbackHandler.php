<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Callback\CallbackDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\CallbackPayloadResolver;

/**
 * Class CallbackHandler.
 */
class CallbackHandler
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
     * @param array|null $payload
     * @return CallbackDTO
     * @throws CallbackException
     */
    public function handle(AuthorizationDTO $authorizationDTO, array $payload = null): CallbackDTO
    {
        $payload = $this->payloadResolver->resolve($authorizationDTO, $payload);

        return CallbackDTO::fromArray($payload);
    }
}
