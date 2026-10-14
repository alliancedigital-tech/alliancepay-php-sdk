<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanDetailsDTO.
 *
 * Response shape of `plan/get_v1`, confirmed against a real captured response. Note: the bank returns
 * `versions` as a JSON object keyed by a string version id ("1", "2", ...), not a plain array — handled
 * via array_values() below.
 */
final class PlanDetailsDTO
{
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_PLAN = 'plan';
    private const PROPERTY_VERSIONS = 'versions';
    private const PROPERTY_SUBSCRIPTION_STATISTICS = 'subscriptionStatistics';

    /**
     * @param PlanVersionDetailsDTO[] $versions
     */
    public function __construct(
        private string $merchantId,
        private PlanSummaryDTO $plan,
        private array $versions,
        private SubscriptionStatisticsDTO $subscriptionStatistics,
    ) {
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return PlanSummaryDTO
     */
    public function getPlan(): PlanSummaryDTO
    {
        return $this->plan;
    }

    /**
     * @return PlanVersionDetailsDTO[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    /**
     * @return SubscriptionStatisticsDTO
     */
    public function getSubscriptionStatistics(): SubscriptionStatisticsDTO
    {
        return $this->subscriptionStatistics;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            merchantId: $data[self::PROPERTY_MERCHANT_ID] ?? '',
            plan: PlanSummaryDTO::fromArray($data[self::PROPERTY_PLAN]),
            versions: array_map(
                static fn (array $version): PlanVersionDetailsDTO => PlanVersionDetailsDTO::fromArray($version),
                array_values($data[self::PROPERTY_VERSIONS] ?? [])
            ),
            subscriptionStatistics: SubscriptionStatisticsDTO::fromArray(
                $data[self::PROPERTY_SUBSCRIPTION_STATISTICS] ?? []
            ),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_PLAN => $this->plan->toArray(),
            self::PROPERTY_VERSIONS => array_map(
                static fn (PlanVersionDetailsDTO $version): array => $version->toArray(),
                $this->versions
            ),
            self::PROPERTY_SUBSCRIPTION_STATISTICS => $this->subscriptionStatistics->toArray(),
        ];
    }
}
