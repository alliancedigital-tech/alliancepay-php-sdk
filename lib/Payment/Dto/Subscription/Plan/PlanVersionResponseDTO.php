<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanVersionResponseDTO.
 *
 * Response shape of `plan/version/update_v1`, confirmed against a real captured response. Distinct from
 * {@see PlanResponseDTO}: it does not carry `status`/`merchantId`/`name`/`description`.
 */
final class PlanVersionResponseDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_VERSION_ID = 'versionId';
    private const PROPERTY_RULES = 'rules';
    private const PROPERTY_APPLIES_DATE_TIME_FROM = 'appliesDateTimeFrom';
    private const PROPERTY_CREATED_AT = 'createdAt';
    private const PROPERTY_UPDATED_AT = 'updatedAt';

    /**
     * @param PlanRuleDTO[] $rules
     */
    public function __construct(
        private string $planId,
        private int $versionId,
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
