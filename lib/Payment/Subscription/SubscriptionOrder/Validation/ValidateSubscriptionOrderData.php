<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Validation\CustomerDataRules;

/**
 * Class ValidateSubscriptionOrderData.
 */
class ValidateSubscriptionOrderData
{
    public const FIELD_RULES = [
        'planId' => [
            'type' => 'string',
            'required' => true,
        ],
        'merchantId' => [
            'type' => 'string',
            'maxLength' => 36,
            'required' => true,
        ],
        'merchantRequestId' => [
            'type' => 'string',
            'maxLength' => 36,
            'required' => true,
        ],
        'purpose' => [
            'type' => 'string',
            'maxLength' => 255,
            'required' => false,
        ],
        'merchantComment' => [
            'type' => 'string',
            'maxLength' => 255,
            'required' => false,
        ],
        'notificationUrl' => [
            'type' => 'string',
            'maxLength' => 255,
            'required' => false,
        ],
        'notificationEncryption' => [
            'types' => ['string', 'boolean'],
            'required' => false,
        ],
        'statusPageType' => [
            'type' => 'string',
            'required' => true,
        ],
        'successUrl' => [
            'type' => 'string',
            'maxLength' => 1000,
            'required' => true,
        ],
        'failUrl' => [
            'type' => 'string',
            'maxLength' => 1000,
            'required' => true,
        ],
        'hppPaymentMethods' => [
            'type' => 'array',
            'required' => true,
        ],
        'directType' => [
            'type' => 'string',
            'required' => false,
        ],
        'customerData' => [
            'type' => 'array',
            'required' => true,
            'properties' => CustomerDataRules::PROPERTIES,
        ],
    ];

    /**
     * @param array $orderData
     * @return array
     * @throws ValidateDataException
     */
    public static function validateSubscriptionOrderRequiredData(array $orderData): array
    {
        $errors = [];

        foreach (self::FIELD_RULES as $field => $rule) {
            $value = $orderData[$field] ?? null;

            if ($rule['required'] === true && self::isEmpty($value)) {
                $errors[] = ['field' => $field, 'message' => 'cannot be empty'];
            }

            if (!self::isEmpty($value)) {
                $errors = array_merge($errors, self::validateFieldValue($field, $value, $rule));
            }

            if (isset($rule['properties'])) {
                $nestedData = is_array($value) ? $value : [];
                $errors = array_merge($errors, self::validateProperties($nestedData, $rule['properties']));
            }
        }

        if (!empty($errors)) {
            throw new ValidateDataException('Validation error', $errors);
        }

        return $orderData;
    }

    /**
     * @param array $orderData
     * @return array
     */
    public static function clearSubscriptionOrderData(array $orderData): array
    {
        foreach (self::FIELD_RULES as $field => $rule) {
            if ($rule['required'] === false && self::isEmpty($orderData[$field] ?? null)) {
                unset($orderData[$field]);
            }
        }

        return $orderData;
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

        if (isset($rule['format'])) {
            $formatError = self::validateFormat($field, $value, $rule['format']);
            if ($formatError !== null) {
                $errors[] = $formatError;
            }
        }

        return $errors;
    }

    /**
     * @param array $data
     * @param array $properties
     * @return array
     */
    private static function validateProperties(array $data, array $properties): array
    {
        $errors = [];

        foreach ($properties as $field => $rule) {
            $value = $data[$field] ?? null;

            if ($rule['required'] === true && self::isEmpty($value)) {
                $errors[] = ['field' => $field, 'message' => 'cannot be empty'];
                continue;
            }

            if (!self::isEmpty($value)) {
                $errors = array_merge($errors, self::validateFieldValue($field, $value, $rule));
            }
        }

        return $errors;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $format
     * @return array|null
     */
    private static function validateFormat(string $field, mixed $value, string $format): ?array
    {
        return match ($format) {
            'iso3166-1-numeric' => (is_string($value) && preg_match('/^\\d{1,3}$/', $value))
                ? null
                : ['field' => $field, 'message' => 'invalid format, expected ISO 3166-1 numeric country code'],
            'digits-only' => (is_string($value) && preg_match('/^\\d+$/', $value))
                ? null
                : ['field' => $field, 'message' => 'invalid format, expected digits only'],
            'date-dmy' => (is_string($value) && preg_match('/^\\d{2}\\.\\d{2}\\.\\d{4}$/', $value))
                ? null
                : ['field' => $field, 'message' => 'invalid format, expected date in d.m.Y format'],
            default => null,
        };
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array $rule
     * @return array|null
     */
    private static function validateType(string $field, mixed $value, array $rule): ?array
    {
        $expectedTypes = $rule['types'] ?? [$rule['type'] ?? 'string'];

        foreach ($expectedTypes as $expectedType) {
            if (self::matchesType($value, $expectedType)) {
                return null;
            }
        }

        return ['field' => $field, 'message' => 'invalid type, expected ' . implode('|', $expectedTypes)];
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
