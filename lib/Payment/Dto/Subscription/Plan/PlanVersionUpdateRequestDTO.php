<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanVersionUpdateRequestDTO.
 *
 * Primary use is the mandatory "infinite schedule" call that must follow every plan create/update
 * ({@see \AlliancePay\Sdk\Payment\Subscription\Plan\PlanLifecycleOrchestrator}); `infiniteVersion` defaults
 * to `true` accordingly. `rules`/`appliesDateTimeFrom` are optional building blocks for version-only updates
 * (e.g. bulk repricing) — their exact combined effect with `infiniteVersion` on this endpoint is not confirmed
 * by a real captured request and should be verified against the bank's test environment before relying on it.
 */
final class PlanVersionUpdateRequestDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_VERSION_ID = 'versionId';
    private const PROPERTY_INFINITE_VERSION = 'infiniteVersion';
    private const PROPERTY_RULES = 'rules';
    private const PROPERTY_APPLIES_DATE_TIME_FROM = 'appliesDateTimeFrom';

    /**
     * @param PlanRuleDTO[]|null $rules
     */
    public function __construct(
        private string $planId,
        private int $versionId,
        private bool $infiniteVersion = true,
        private ?array $rules = null,
        private ?string $appliesDateTimeFrom = null,
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
     * @return bool
     */
    public function isInfiniteVersion(): bool
    {
        return $this->infiniteVersion;
    }

    /**
     * @return PlanRuleDTO[]|null
     */
    public function getRules(): ?array
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
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            planId: $data[self::PROPERTY_PLAN_ID],
            versionId: (int)$data[self::PROPERTY_VERSION_ID],
            infiniteVersion: (bool)($data[self::PROPERTY_INFINITE_VERSION] ?? true),
            rules: isset($data[self::PROPERTY_RULES])
                ? array_map(static fn (array $rule): PlanRuleDTO => PlanRuleDTO::fromArray($rule), $data[self::PROPERTY_RULES])
                : null,
            appliesDateTimeFrom: $data[self::PROPERTY_APPLIES_DATE_TIME_FROM] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::PROPERTY_PLAN_ID => $this->planId,
            self::PROPERTY_VERSION_ID => $this->versionId,
            self::PROPERTY_INFINITE_VERSION => $this->infiniteVersion,
        ];

        if ($this->rules !== null) {
            $data[self::PROPERTY_RULES] = array_map(
                static fn (PlanRuleDTO $rule): array => $rule->toArray(),
                $this->rules
            );
        }

        if ($this->appliesDateTimeFrom !== null) {
            $data[self::PROPERTY_APPLIES_DATE_TIME_FROM] = $this->appliesDateTimeFrom;
        }

        return $data;
    }
}
