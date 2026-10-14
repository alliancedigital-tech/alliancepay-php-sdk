<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\CreateSubscriptionOrderException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class CreateSubscriptionOrder.
 *
 * Enrolls a customer into a plan via the Hosted Payment Page redirect flow. Per the source specification
 * (section 4.2), the consuming application must enforce two rules the SDK itself cannot: no guest checkout
 * (a subscription always needs an owner) and no mixed cart (a single order cannot combine a subscription
 * with a regular purchase, or two different plans).
 *
 * ⚠ `merchantRequestId` is NOT a safe retry key for this call: retrying with the same value after a
 * network failure creates a new subscription order on the bank's side rather than returning the original
 * one. A caller that wants retry-safety must implement its own deduplication before calling this again.
 */
class CreateSubscriptionOrder
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param SubscriptionOrderRequestDTO $orderRequestDto
     * @param AuthorizationDTO $authorizationDto
     * @return SubscriptionOrderResponseDTO
     * @throws CreateSubscriptionOrderException
     */
    public function createSubscriptionOrder(
        SubscriptionOrderRequestDTO $orderRequestDto,
        AuthorizationDTO $authorizationDto
    ): SubscriptionOrderResponseDTO {
        try {
            $this->httpClient->applyRequestId($orderRequestDto->getMerchantRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_CREATE_SUBSCRIPTION_ORDER,
                $orderRequestDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getCreateSubscriptionOrderUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, CreateSubscriptionOrderException::class);

            return SubscriptionOrderResponseDTO::fromArray($result['body']);
        } catch (CreateSubscriptionOrderException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new CreateSubscriptionOrderException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new CreateSubscriptionOrderException(
                message: 'An unexpected error occurred during subscription order creation',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
