<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanStatusUpdateRequestDTO.
 */
final class PlanStatusUpdateRequestDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_STATUS = 'status';

    public function __construct(
        private string $planId,
        private string $merchantId,
        private string $status,
    ) {
    }

    /**
     * @return string
     */
    public function getPlanId(): string
    {
        return $this->planId;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
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
            planId: $data[self::PROPERTY_PLAN_ID],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            status: $data[self::PROPERTY_STATUS],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_PLAN_ID => $this->planId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_STATUS => $this->status,
        ];
    }
}
