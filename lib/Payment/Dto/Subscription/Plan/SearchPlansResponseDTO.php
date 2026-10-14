<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class SearchPlansResponseDTO.
 *
 * Response shape of `plan/search_v1`, confirmed against a real captured response.
 */
final class SearchPlansResponseDTO
{
    private const PROPERTY_CURRENT_PAGE = 'currentPage';
    private const PROPERTY_PLANS = 'plans';

    /**
     * @param PlanSummaryDTO[] $plans
     */
    public function __construct(
        private int $currentPage,
        private array $plans,
    ) {
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return PlanSummaryDTO[]
     */
    public function getPlans(): array
    {
        return $this->plans;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            currentPage: (int)($data[self::PROPERTY_CURRENT_PAGE] ?? 0),
            plans: array_map(
                static fn (array $plan): PlanSummaryDTO => PlanSummaryDTO::fromArray($plan),
                $data[self::PROPERTY_PLANS] ?? []
            ),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_CURRENT_PAGE => $this->currentPage,
            self::PROPERTY_PLANS => array_map(
                static fn (PlanSummaryDTO $plan): array => $plan->toArray(),
                $this->plans
            ),
        ];
    }
}
