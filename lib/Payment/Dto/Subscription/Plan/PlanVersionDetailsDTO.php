<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanVersionDetailsDTO.
 *
 * One entry of `plan/get_v1`'s `versions` map. Note: the bank returns `versions` and each version's `rules`
 * as JSON objects keyed by a string index ("1", "2", ...), not as plain arrays — handled by
 * {@see PlanDetailsDTO::fromArray()} via array_values() before construction here.
 */
final class PlanVersionDetailsDTO
{
    private const PROPERTY_VERSION_ID = 'versionId';
    private const PROPERTY_APPLIES_DATE_TIME_FROM = 'appliesDateTimeFrom';
    private const PROPERTY_RULES = 'rules';

    /**
     * @param PlanRuleDTO[] $rules
     */
    public function __construct(
        private int $versionId,
        private ?string $appliesDateTimeFrom,
        private array $rules,
    ) {
    }

    /**
     * @return int
     */
    public function getVersionId(): int
    {
        return $this->versionId;
    }

    /**
     * @return string|null
     */
    public function getAppliesDateTimeFrom(): ?string
    {
        return $this->appliesDateTimeFrom;
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
     */
    public static function fromArray(array $data): self
    {
        return new self(
            versionId: (int)$data[self::PROPERTY_VERSION_ID],
            appliesDateTimeFrom: $data[self::PROPERTY_APPLIES_DATE_TIME_FROM] ?? null,
            rules: array_map(
                static fn (array $rule): PlanRuleDTO => PlanRuleDTO::fromArray($rule),
                array_values($data[self::PROPERTY_RULES] ?? [])
            ),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_VERSION_ID => $this->versionId,
            self::PROPERTY_APPLIES_DATE_TIME_FROM => $this->appliesDateTimeFrom,
            self::PROPERTY_RULES => array_map(
                static fn (PlanRuleDTO $rule): array => $rule->toArray(),
                $this->rules
            ),
        ];
    }
}
