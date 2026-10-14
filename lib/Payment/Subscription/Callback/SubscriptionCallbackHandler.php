<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Callback\SubscriptionCallbackDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\CallbackPayloadResolver;

/**
 * Class SubscriptionCallbackHandler.
 *
 * Shares its raw-body-read/signature-verification pipeline with the one-shot
 * {@see \AlliancePay\Sdk\Payment\Callback\CallbackHandler} via {@see CallbackPayloadResolver} — signature
 * verification is the unconditional first step, with no fallback branch that trusts an unverified payload.
 *
 * Resolution is strictly by `hppOrderId` (see {@see SubscriptionCallbackDTO}) — this method intentionally
 * takes no plan/product identifier, since a single plan can have many active subscriptions. Idempotency by
 * operation id and safe merging of partial deliveries are the consuming application's responsibility, since
 * this SDK holds no state; {@see SubscriptionCallbackDTO::getRawPayload()} exists to make that safe.
 */
class SubscriptionCallbackHandler
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
     * @return SubscriptionCallbackDTO
     * @throws CallbackException
     */
    public function handle(AuthorizationDTO $authorizationDTO, array $payload = null): SubscriptionCallbackDTO
    {
        $payload = $this->payloadResolver->resolve($authorizationDTO, $payload);

        return SubscriptionCallbackDTO::fromArray($payload);
    }
}
