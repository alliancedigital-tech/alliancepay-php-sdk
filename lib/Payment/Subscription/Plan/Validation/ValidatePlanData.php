<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanRuleDTO;

/**
 * Class ValidatePlanData.
 */
class ValidatePlanData
{
    public const FIELD_RULES = [
        'name' => [
            'type' => 'string',
            'maxLength' => 255,
            'required' => true,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 1000,
            // Required despite the source specification marking it optional ("description?") — the bank's
            // plan/create_v1 rejects a blank/missing description with a VALIDATION_ERROR ("NotBlank"),
            // confirmed against a real captured error response.
            'required' => true,
        ],
        'categorySubcategoryIndicator' => [
            'type' => 'string',
            'maxLength' => 50,
            'required' => true,
        ],
        'merchantId' => [
            'type' => 'string',
            'maxLength' => 36,
            'required' => true,
        ],
        'planId' => [
            'type' => 'string',
            'maxLength' => 64,
            'required' => false,
        ],
        'appliesDateTimeFrom' => [
            'type' => 'string',
            'required' => false,
        ],
        'appliesDateTimeTo' => [
            'type' => 'string',
            'required' => false,
        ],
        'rules' => [
            'type' => 'array',
            'required' => true,
        ],
    ];

    private const RULE_ITEM_FIELDS = [
        'paymentNumberFrom' => ['type' => 'int', 'required' => true],
        'paymentNumberTo' => ['type' => 'int', 'required' => true],
        'intervalValue' => ['type' => 'int', 'required' => true],
        'intervalUnit' => ['type' => 'string', 'required' => true],
        'coinAmount' => ['type' => 'int', 'required' => true, 'min' => 0],
        'currency' => ['type' => 'string', 'required' => true, 'enum' => PlanRuleDTO::SUPPORTED_CURRENCIES],
    ];

    /**
     * @param array $planData
     * @return array
     * @throws ValidateDataException
     */
    public static function validatePlanRequiredData(array $planData): array
    {
        $errors = [];

        foreach (self::FIELD_RULES as $field => $rule) {
            $value = $planData[$field] ?? null;

            if ($rule['required'] === true && self::isEmpty($value)) {
                $errors[] = ['field' => $field, 'message' => 'cannot be empty'];
                continue;
            }

            if (!self::isEmpty($value)) {
                $errors = array_merge($errors, self::validateFieldValue($field, $value, $rule));
            }
        }

        if (!self::isEmpty($planData['rules'] ?? null) && is_array($planData['rules'])) {
            $errors = array_merge($errors, self::validateRules($planData['rules']));
        }

        if (
            isset($planData['appliesDateTimeFrom'], $planData['appliesDateTimeTo'])
            && $planData['appliesDateTimeFrom'] !== ''
            && $planData['appliesDateTimeTo'] !== ''
            && $planData['appliesDateTimeFrom'] >= $planData['appliesDateTimeTo']
        ) {
            $errors[] = [
                'field' => 'appliesDateTimeTo',
                'message' => 'must be later than appliesDateTimeFrom',
            ];
        }

        if (!empty($errors)) {
            throw new ValidateDataException('Validation error', $errors);
        }

        return $planData;
    }

    /**
     * @param array $rules
     * @return array
     */
    private static function validateRules(array $rules): array
    {
        $errors = [];
        $ranges = [];

        foreach ($rules as $index => $rule) {
            if (!is_array($rule)) {
                $errors[] = ['field' => "rules[$index]", 'message' => 'must be an array'];
                continue;
            }

            foreach (self::RULE_ITEM_FIELDS as $itemField => $itemRule) {
                $itemValue = $rule[$itemField] ?? null;
                $itemKey = "rules[$index].$itemField";

                if ($itemRule['required'] === true && self::isEmpty($itemValue)) {
                    $errors[] = ['field' => $itemKey, 'message' => 'cannot be empty'];
                    continue;
                }

                if (!self::isEmpty($itemValue)) {
                    $errors = array_merge($errors, self::validateFieldValue($itemKey, $itemValue, $itemRule));
                }
            }

            if (
                isset($rule['paymentNumberFrom'], $rule['paymentNumberTo'])
                && is_int($rule['paymentNumberFrom'])
                && is_int($rule['paymentNumberTo'])
            ) {
                if ($rule['paymentNumberFrom'] > $rule['paymentNumberTo']) {
                    $errors[] = [
                        'field' => "rules[$index].paymentNumberFrom",
                        'message' => 'must not be greater than paymentNumberTo',
                    ];
                } else {
                    $ranges[] = [$rule['paymentNumberFrom'], $rule['paymentNumberTo'], $index];
                }
            }
        }

        usort($ranges, fn (array $a, array $b): int => $a[0] <=> $b[0]);

        for ($i = 1; $i < count($ranges); $i++) {
            if ($ranges[$i][0] <= $ranges[$i - 1][1]) {
                $errors[] = [
                    'field' => "rules[{$ranges[$i][2]}].paymentNumberFrom",
                    'message' => 'overlaps with rules[' . $ranges[$i - 1][2] . ']',
                ];
            }
        }

        return $errors;
    }

    /**
     * @param array $planData
     * @return array
     */
    public static function clearPlanData(array $planData): array
    {
        foreach (self::FIELD_RULES as $field => $rule) {
            if ($rule['required'] === false && self::isEmpty($planData[$field] ?? null)) {
                unset($planData[$field]);
            }
        }

        return $planData;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array $rule
     * @return array
     */
    private static function validateFieldValue(string $field, mixed $value, array $rule): array
    {
        $errors = [];

        $typeError = self::validateType($field, $value, $rule);
        if ($typeError !== null) {
            return [$typeError];
        }

        if (isset($rule['maxLength']) && is_string($value) && mb_strlen($value) > $rule['maxLength']) {
            $errors[] = ['field' => $field, 'message' => 'exceeds maxLength of ' . $rule['maxLength']];
        }

        if (isset($rule['min']) && is_int($value) && $value < $rule['min']) {
            $errors[] = ['field' => $field, 'message' => 'must be at least ' . $rule['min']];
        }

        if (isset($rule['enum']) && !in_array($value, $rule['enum'], true)) {
            $errors[] = [
                'field' => $field,
                'message' => 'invalid value, allowed: ' . implode(', ', $rule['enum']),
            ];
        }

        return $errors;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array $rule
     * @return array|null
     */
    private static function validateType(string $field, mixed $value, array $rule): ?array
    {
        $expectedType = $rule['type'] ?? 'string';

        if (self::matchesType($value, $expectedType)) {
            return null;
        }

        if ($expectedType === 'int' && is_string($value) && is_numeric($value) && !str_contains($value, '.')) {
            return null;
        }

        return ['field' => $field, 'message' => 'invalid type, expected ' . $expectedType];
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    private static function matchesType(mixed $value, string $type): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'int' => is_int($value),
            'boolean' => is_bool($value),
            'array' => is_array($value),
            default => false,
        };
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }
}
