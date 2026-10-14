<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class SearchPlansRequestDTO.
 *
 * Request body confirmed against a real captured request for `plan/search_v1`:
 * {"merchantId": "...", "filters": [{"operator": "EQUALS", "value": "ACTIVE", "field": "status"}]}.
 */
final class SearchPlansRequestDTO
{
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_FILTERS = 'filters';

    /**
     * @param PlanSearchFilterDTO[] $filters
     */
    public function __construct(
        private string $merchantId,
        private array $filters = [],
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
     * @return PlanSearchFilterDTO[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            filters: array_map(
                static fn (array $filter): PlanSearchFilterDTO => PlanSearchFilterDTO::fromArray($filter),
                $data[self::PROPERTY_FILTERS] ?? []
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
            self::PROPERTY_FILTERS => array_map(
                static fn (PlanSearchFilterDTO $filter): array => $filter->toArray(),
                $this->filters
            ),
        ];
    }
}
