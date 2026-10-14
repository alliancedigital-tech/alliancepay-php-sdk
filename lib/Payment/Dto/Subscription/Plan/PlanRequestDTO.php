<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Subscription\Plan\Validation\ValidatePlanData;

/**
 * Class PlanRequestDTO.
 */
final class PlanRequestDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_NAME = 'name';
    private const PROPERTY_DESCRIPTION = 'description';
    private const PROPERTY_CATEGORY_SUBCATEGORY_INDICATOR = 'categorySubcategoryIndicator';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_APPLIES_DATE_TIME_FROM = 'appliesDateTimeFrom';
    private const PROPERTY_APPLIES_DATE_TIME_TO = 'appliesDateTimeTo';
    private const PROPERTY_RULES = 'rules';

    /**
     * @param PlanRuleDTO[] $rules
     */
    public function __construct(
        private string $name,
        private string $categorySubcategoryIndicator,
        private string $merchantId,
        private array $rules,
        private ?string $planId = null,
        private ?string $description = null,
        private ?string $appliesDateTimeFrom = null,
        private ?string $appliesDateTimeTo = null,
    ) {
    }

    /**
     * @return string|null
     */
    public function getPlanId(): ?string
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
    public function getCategorySubcategoryIndicator(): string
    {
        return $this->categorySubcategoryIndicator;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return string|null
     */
    public function getAppliesDateTimeFrom(): ?string
    {
        return $this->appliesDateTimeFrom;
    }

    /**
     * @return string|null
     */
    public function getAppliesDateTimeTo(): ?string
    {
        return $this->appliesDateTimeTo;
    }

    /**
     * @return PlanRuleDTO[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $data
     * @return self
     * @throws ValidateDataException
     */
    public static function fromArray(array $data): self
    {
        $data = ValidatePlanData::validatePlanRequiredData($data);

        return new self(
            name: $data[self::PROPERTY_NAME],
            categorySubcategoryIndicator: $data[self::PROPERTY_CATEGORY_SUBCATEGORY_INDICATOR],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            rules: array_map(
                static fn (array $rule): PlanRuleDTO => PlanRuleDTO::fromArray($rule),
                $data[self::PROPERTY_RULES]
            ),
            planId: $data[self::PROPERTY_PLAN_ID] ?? null,
            description: $data[self::PROPERTY_DESCRIPTION] ?? null,
            appliesDateTimeFrom: $data[self::PROPERTY_APPLIES_DATE_TIME_FROM] ?? null,
            appliesDateTimeTo: $data[self::PROPERTY_APPLIES_DATE_TIME_TO] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::PROPERTY_PLAN_ID => $this->planId,
            self::PROPERTY_NAME => $this->name,
            self::PROPERTY_DESCRIPTION => $this->description,
            self::PROPERTY_CATEGORY_SUBCATEGORY_INDICATOR => $this->categorySubcategoryIndicator,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_APPLIES_DATE_TIME_FROM => $this->appliesDateTimeFrom,
            self::PROPERTY_APPLIES_DATE_TIME_TO => $this->appliesDateTimeTo,
            self::PROPERTY_RULES => array_map(
                static fn (PlanRuleDTO $rule): array => $rule->toArray(),
                $this->rules
            ),
        ];

        return ValidatePlanData::clearPlanData($data);
    }
}
