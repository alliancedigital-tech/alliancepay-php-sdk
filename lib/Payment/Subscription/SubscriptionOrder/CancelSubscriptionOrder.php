<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\CancelSubscriptionOrderException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderCancelRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderCancelResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class CancelSubscriptionOrder.
 *
 * Resolved strictly by the bank's unique order id (`hppOrderId`), never by a local subscription/plan id —
 * per the source specification, local history (scheduled payments, rule snapshots) should never be deleted
 * by the consuming application on cancellation, only its status changed.
 */
class CancelSubscriptionOrder
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param SubscriptionOrderCancelRequestDTO $cancelRequestDto
     * @param AuthorizationDTO $authorizationDto
     * @return SubscriptionOrderCancelResponseDTO
     * @throws CancelSubscriptionOrderException
     */
    public function cancelSubscriptionOrder(
        SubscriptionOrderCancelRequestDTO $cancelRequestDto,
        AuthorizationDTO $authorizationDto
    ): SubscriptionOrderCancelResponseDTO {
        try {
            $this->httpClient->applyRequestId(
                $cancelRequestDto->getMerchantRequestId() ?? GenerateRequestIdentification::generateRequestId()
            );
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_CANCEL_SUBSCRIPTION_ORDER,
                $cancelRequestDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getCancelSubscriptionOrderUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, CancelSubscriptionOrderException::class);

            return SubscriptionOrderCancelResponseDTO::fromArray($result['body']);
        } catch (CancelSubscriptionOrderException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new CancelSubscriptionOrderException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new CancelSubscriptionOrderException(
                message: 'An unexpected error occurred during subscription order cancellation',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
