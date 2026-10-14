<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanVersionDeleteRequestDTO.
 */
final class PlanVersionDeleteRequestDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_VERSION_ID = 'versionId';

    public function __construct(
        private string $planId,
        private int $versionId,
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
     * @return int
     */
    public function getVersionId(): int
    {
        return $this->versionId;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            planId: $data[self::PROPERTY_PLAN_ID],
            versionId: (int)$data[self::PROPERTY_VERSION_ID],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_PLAN_ID => $this->planId,
            self::PROPERTY_VERSION_ID => $this->versionId,
        ];
    }
}
