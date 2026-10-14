<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Payment;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\TriggerRecurringPaymentException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Payment\RecurringPaymentTriggerRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Payment\RecurringPaymentTriggerResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class TriggerRecurringPayment.
 *
 * Manual, support/ops-only trigger for a recurring charge — used when the bank's own scheduled trigger
 * failed and waiting for the next billing cycle is not acceptable. This is NOT a customer-facing operation;
 * the SDK does not gate who may call it, so the consuming application must restrict it to privileged users.
 */
class TriggerRecurringPayment
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param RecurringPaymentTriggerRequestDTO $triggerDto
     * @param AuthorizationDTO $authorizationDto
     * @return RecurringPaymentTriggerResponseDTO
     * @throws TriggerRecurringPaymentException
     */
    public function triggerRecurringPayment(
        RecurringPaymentTriggerRequestDTO $triggerDto,
        AuthorizationDTO $authorizationDto
    ): RecurringPaymentTriggerResponseDTO {
        try {
            $this->httpClient->applyRequestId($triggerDto->getMerchantRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_RECURRING_SUBSCRIPTION_PAYMENT,
                $triggerDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getRecurringSubscriptionPaymentUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, TriggerRecurringPaymentException::class);

            return RecurringPaymentTriggerResponseDTO::fromArray($result['body']);
        } catch (TriggerRecurringPaymentException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new TriggerRecurringPaymentException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new TriggerRecurringPaymentException(
                message: 'An unexpected error occurred while triggering the recurring payment',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
