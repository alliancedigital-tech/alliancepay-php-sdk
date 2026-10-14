<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanCreationResultDTO.
 *
 * Result of {@see \AlliancePay\Sdk\Payment\Subscription\Plan\PlanLifecycleOrchestrator}. The bank silently
 * applies a hidden expiration to a plan unless the mandatory "infinite schedule" call
 * (`plan/version/update_v1`) succeeds right after plan create/update — this DTO makes that partial-failure
 * state explicit instead of letting the caller treat a successful `planResponse` alone as "fully done".
 */
final class PlanCreationResultDTO
{
    public function __construct(
        private PlanResponseDTO $planResponse,
        private bool $infiniteScheduleApplied,
        private ?string $infiniteScheduleError = null,
    ) {
    }

    /**
     * @return PlanResponseDTO
     */
    public function getPlanResponse(): PlanResponseDTO
    {
        return $this->planResponse;
    }

    /**
     * @return bool
     */
    public function isInfiniteScheduleApplied(): bool
    {
        return $this->infiniteScheduleApplied;
    }

    /**
     * @return string|null
     */
    public function getInfiniteScheduleError(): ?string
    {
        return $this->infiniteScheduleError;
    }

    /**
     * @return bool
     */
    public function isFullySuccessful(): bool
    {
        return $this->infiniteScheduleApplied === true;
    }
}
