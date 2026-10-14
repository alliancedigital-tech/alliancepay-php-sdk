<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanSummaryDTO.
 *
 * Short plan representation without rules — used both by `plan/search_v1` (list items) and as the `plan`
 * sub-object of `plan/get_v1`'s response, confirmed against real captured responses.
 */
final class PlanSummaryDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_NAME = 'name';
    private const PROPERTY_DESCRIPTION = 'description';
    private const PROPERTY_STATUS = 'status';
    private const PROPERTY_CATEGORY_SUBCATEGORY_INDICATOR = 'categorySubcategoryIndicator';
    private const PROPERTY_CREATED_AT = 'createdAt';
    private const PROPERTY_UPDATED_AT = 'updatedAt';

    public function __construct(
        private string $planId,
        private string $name,
        private ?string $description,
        private string $status,
        private ?string $categorySubcategoryIndicator,
        private ?string $createdAt,
        private ?string $updatedAt,
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getCategorySubcategoryIndicator(): ?string
    {
        return $this->categorySubcategoryIndicator;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            planId: $data[self::PROPERTY_PLAN_ID],
            name: $data[self::PROPERTY_NAME] ?? '',
            description: $data[self::PROPERTY_DESCRIPTION] ?? null,
            status: $data[self::PROPERTY_STATUS] ?? '',
            categorySubcategoryIndicator: $data[self::PROPERTY_CATEGORY_SUBCATEGORY_INDICATOR] ?? null,
            createdAt: $data[self::PROPERTY_CREATED_AT] ?? null,
            updatedAt: $data[self::PROPERTY_UPDATED_AT] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_PLAN_ID => $this->planId,
            self::PROPERTY_NAME => $this->name,
            self::PROPERTY_DESCRIPTION => $this->description,
            self::PROPERTY_STATUS => $this->status,
            self::PROPERTY_CATEGORY_SUBCATEGORY_INDICATOR => $this->categorySubcategoryIndicator,
            self::PROPERTY_CREATED_AT => $this->createdAt,
            self::PROPERTY_UPDATED_AT => $this->updatedAt,
        ];
    }
}
