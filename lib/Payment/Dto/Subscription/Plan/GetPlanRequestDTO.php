<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class GetPlanRequestDTO.
 *
 * Request body confirmed against a real captured request for `plan/get_v1`: only `planId` is sent,
 * without `merchantId`.
 */
final class GetPlanRequestDTO
{
    private const PROPERTY_PLAN_ID = 'planId';

    public function __construct(
        private string $planId,
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
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            planId: $data[self::PROPERTY_PLAN_ID],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_PLAN_ID => $this->planId,
        ];
    }
}
