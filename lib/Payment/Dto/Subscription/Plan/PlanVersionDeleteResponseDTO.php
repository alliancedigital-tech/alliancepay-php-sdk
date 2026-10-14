<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanVersionDeleteResponseDTO.
 *
 * Response shape confirmed against a real captured response for `plan/delete_v1`.
 */
final class PlanVersionDeleteResponseDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_VERSION_ID = 'versionId';
    private const PROPERTY_VERSION_STATUS = 'versionStatus';

    public const VERSION_STATUS_DELETED = 'DELETED';

    public function __construct(
        private string $planId,
        private int $versionId,
        private string $versionStatus,
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
     * @return string
     */
    public function getVersionStatus(): string
    {
        return $this->versionStatus;
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
            versionStatus: $data[self::PROPERTY_VERSION_STATUS],
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
            self::PROPERTY_VERSION_STATUS => $this->versionStatus,
        ];
    }
}
