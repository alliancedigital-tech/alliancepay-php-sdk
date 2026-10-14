<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Payment;

/**
 * Class RecurringPaymentTriggerResponseDTO.
 *
 * Shape confirmed by the bank's own API doc
 * (`Recurring payment docs/api_scheduled_payments-recurring_subscription_v1.md`): a flat
 * `operationId`/`ecomOperationId`/`status` echo — NOT the `hppOrderId`/`orderStatus`/nested
 * `operation` shape an earlier revision of this DTO guessed (that guess was never confirmed by a
 * real captured response and turned out wrong once the official doc was found).
 */
final class RecurringPaymentTriggerResponseDTO
{
    private const PROPERTY_OPERATION_ID = 'operationId';
    private const PROPERTY_ECOM_OPERATION_ID = 'ecomOperationId';
    private const PROPERTY_STATUS = 'status';

    public function __construct(
        private string $operationId,
        private string $ecomOperationId,
        private string $status,
    ) {
    }

    /**
     * @return string
     */
    public function getOperationId(): string
    {
        return $this->operationId;
    }

    /**
     * @return string
     */
    public function getEcomOperationId(): string
    {
        return $this->ecomOperationId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            operationId: $data[self::PROPERTY_OPERATION_ID] ?? '',
            ecomOperationId: $data[self::PROPERTY_ECOM_OPERATION_ID] ?? '',
            status: $data[self::PROPERTY_STATUS] ?? '',
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_OPERATION_ID => $this->operationId,
            self::PROPERTY_ECOM_OPERATION_ID => $this->ecomOperationId,
            self::PROPERTY_STATUS => $this->status,
        ];
    }
}
