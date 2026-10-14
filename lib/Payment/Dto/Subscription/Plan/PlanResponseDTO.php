<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanResponseDTO.
 *
 * Response shape of `plan/create_v1` and `plan/update_v1`, confirmed against real captured responses.
 */
final class PlanResponseDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_VERSION_ID = 'versionId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_NAME = 'name';
    private const PROPERTY_DESCRIPTION = 'description';
    private const PROPERTY_STATUS = 'status';
    private const PROPERTY_RULES = 'rules';
    private const PROPERTY_APPLIES_DATE_TIME_FROM = 'appliesDateTimeFrom';
    private const PROPERTY_CREATED_AT = 'createdAt';
    private const PROPERTY_UPDATED_AT = 'updatedAt';

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PAUSED = 'PAUSED';
    public const STATUS_CANCELED = 'CANCELED';
    public const STATUS_ERROR = 'ERROR';

    /**
     * @param PlanRuleDTO[] $rules
     */
    public function __construct(
        private string $planId,
        private int $versionId,
        private string $merchantId,
        private string $name,
        private ?string $description,
        private string $status,
        private array $rules,
        private ?string $appliesDateTimeFrom,
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
     * @return int
     */
    public function getVersionId(): int
    {
        return $this->versionId;
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
     * @return PlanRuleDTO[]
     */
    public function getRules(): array
    {
        return $this->rules;
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
            versionId: (int)$data[self::PROPERTY_VERSION_ID],
            merchantId: $data[self::PROPERTY_MERCHANT_ID] ?? '',
            name: $data[self::PROPERTY_NAME] ?? '',
            description: $data[self::PROPERTY_DESCRIPTION] ?? null,
            status: $data[self::PROPERTY_STATUS] ?? '',
            rules: array_map(
                static fn (array $rule): PlanRuleDTO => PlanRuleDTO::fromArray($rule),
                $data[self::PROPERTY_RULES] ?? []
            ),
            appliesDateTimeFrom: $data[self::PROPERTY_APPLIES_DATE_TIME_FROM] ?? null,
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
            self::PROPERTY_VERSION_ID => $this->versionId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_NAME => $this->name,
            self::PROPERTY_DESCRIPTION => $this->description,
            self::PROPERTY_STATUS => $this->status,
            self::PROPERTY_RULES => array_map(
                static fn (PlanRuleDTO $rule): array => $rule->toArray(),
                $this->rules
            ),
            self::PROPERTY_APPLIES_DATE_TIME_FROM => $this->appliesDateTimeFrom,
            self::PROPERTY_CREATED_AT => $this->createdAt,
            self::PROPERTY_UPDATED_AT => $this->updatedAt,
        ];
    }
}
