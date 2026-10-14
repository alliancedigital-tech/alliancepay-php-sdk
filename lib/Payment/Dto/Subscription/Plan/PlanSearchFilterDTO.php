<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanSearchFilterDTO.
 *
 * One filter entry of `plan/search_v1`'s request body, confirmed against a real captured request:
 * {"operator": "EQUALS", "value": "ACTIVE", "field": "status"}. Only the "EQUALS" operator value is
 * confirmed — other operators may exist but are not documented, so `operator` is not restricted to an enum.
 */
final class PlanSearchFilterDTO
{
    private const PROPERTY_OPERATOR = 'operator';
    private const PROPERTY_VALUE = 'value';
    private const PROPERTY_FIELD = 'field';

    public const OPERATOR_EQUALS = 'EQUALS';

    public function __construct(
        private string $field,
        private string $value,
        private string $operator = self::OPERATOR_EQUALS,
    ) {
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            field: $data[self::PROPERTY_FIELD],
            value: $data[self::PROPERTY_VALUE],
            operator: $data[self::PROPERTY_OPERATOR] ?? self::OPERATOR_EQUALS,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_OPERATOR => $this->operator,
            self::PROPERTY_VALUE => $this->value,
            self::PROPERTY_FIELD => $this->field,
        ];
    }
}
