<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Order\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;

/**
 * Class ValidateOrderData.
 */
class ValidateOrderData
{
    // Kept for backwards compatibility
    public const REQUIRED_CRETE_ORDER_DATA_FIELDS = [
        'merchantRequestId',
        'merchantId',
        'hppPayType',
        'coinAmount',
        'paymentMethods',
        'successUrl',
        'failUrl',
        'statusPageType',
    ];

    // Kept for backwards compatibility
    public const ADDITIONAL_CREATE_ORDER_DATA_FIELDS = [
        'merchantComment',
        'hppTryMode',
        'directType',
        'expirationTimeMinutes',
        'language',
        'notificationUrl',
        'notificationEncryption',
        'purpose',
        'priorityBankCode',
        'paymentCategoryGoal',
        'generateQrNbu'
    ];

    // Kept for backwards compatibility
    public const REQUIRED_CUSTOMER_DATA_FIELD = 'customerData';

    // Kept for backwards compatibility
    public const REQUIRED_CUSTOMER_DATA_FIELD_SENDER_CUSTOMER_ID = 'senderCustomerId';

    // Kept for backwards compatibility
    public const ADDITIONAL_CUSTOMER_DATA_FIELDS = [
        'senderFirstName',
        'senderLastName',
        'senderMiddleName',
        'senderEmail',
        'senderCountry',
        'senderRegion',
        'senderCity',
        'senderStreet',
        'senderAdditionalAddress',
        'senderItn',
        'senderPassport',
        'senderIp',
        'senderPhone',
        'senderBirthday',
        'senderGender',
        'senderZipCode'
    ];

    public const FIELD_RULES = [
        'merchantRequestId' => [
            'type' => 'string',
            'maxLength' => 36,
            'required' => true,
        ],
        'merchantId' => [
            'type' => 'string',
            'maxLength' => 36,
            'required' => true,
        ],
        'hppPayType' => [
            'type' => 'string',
            'required' => true,
        ],
        'directType' => [
            'type' => 'string',
            'required' => false,
        ],
        'hppTryMode' => [
            'type' => 'string',
            'required' => false,
        ],
        'expirationTimeMinutes' => [
            'type' => 'int',
            'maxDigits' => 4,
            'required' => false,
        ],
        'coinAmount' => [
            'type' => 'int',
            'required' => true,
        ],
        'paymentMethods' => [
            'type' => 'array',
            'required' => true,
        ],
        'language' => [
            'type' => 'string',
            'maxLength' => 50,
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
        'statusPageType' => [
            'type' => 'string',
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
        'priorityBankCode' => [
            'type' => 'string',
            'required' => false,
        ],
        'paymentCategoryGoal' => [
            'type' => 'string',
            'required' => false,
        ],
        'generateQrNbu' => [
            'type' => 'boolean',
            'required' => false,
        ],
        'customerData' => [
            'type' => 'array',
            'required' => true,
            'properties' => [
                'senderCustomerId' => [
                    'type' => 'string',
                    'maxLength' => 255,
                    'required' => true,
                ],
                'senderFirstName' => [
                    'type' => 'string',
                    'maxLength' => 30,
                    'required' => false,
                ],
                'senderLastName' => [
                    'type' => 'string',
                    'maxLength' => 30,
                    'required' => false,
                ],
                'senderMiddleName' => [
                    'type' => 'string',
                    'maxLength' => 30,
                    'required' => false,
                ],
                'senderEmail' => [
                    'type' => 'string',
                    'maxLength' => 256,
                    'required' => false,
                ],
                'senderCountry' => [
                    'type'    => 'string',
                    'maxLength' => 3,
                    'format'  => 'iso3166-1-numeric',
                    'required' => false,
                ],
                'senderRegion' => [
                    'type' => 'string',
                    'maxLength' => 255,
                    'required' => false,
                ],
                'senderCity' => [
                    'type' => 'string',
                    'maxLength' => 25,
                    'required' => false,
                ],
                'senderStreet' => [
                    'type' => 'string',
                    'maxLength' => 35,
                    'required' => false,
                ],
                'senderAdditionalAddress' => [
                    'type' => 'string',
                    'maxLength' => 255,
                    'required' => false,
                ],
                'senderItn' => [
                    'type' => 'string',
                    'maxLength' => 20,
                    'required' => false,
                ],
                'senderPassport' => [
                    'type' => 'string',
                    'maxLength' => 255,
                    'required' => false,
                ],
                'senderIp' => [
                    'type' => 'string',
                    'maxLength' => 50,
                    'required' => false,
                ],
                'senderPhone' => [
                    'type'      => 'string',
                    'maxLength' => 20,
                    'format'    => 'digits-only',
                    'required'  => false,
                ],
                'senderBirthday' => [
                    'type'      => 'string',
                    'maxLength' => 50,
                    'format'    => 'date-dmy',
                    'required'  => false,
                ],
                'senderGender' => [
                    'type' => 'string',
                    'maxLength' => 50,
                    'required' => false,
                ],
                'senderZipCode' => [
                    'type' => 'string',
                    'maxLength' => 50,
                    'required' => false,
                ],
            ],
        ],
    ];

    /**
     * @param array $orderData
     * @return array
     * @throws ValidateDataException
     */
    public static function validateOrderRequiredData(
        array $orderData
    ): array
    {
        $errors = [];

        foreach (self::FIELD_RULES as $field => $rule) {
            $value = $orderData[$field] ?? null;

            if ($rule['required'] === true && self::isEmpty($value)) {
                $errors[] = ['field' => $field, 'message' => 'cannot be empty'];
                // Do not skip — nested required fields must still be reported
            }

            if (!self::isEmpty($value)) {
                $errors = array_merge($errors, self::validateFieldValue($field, $value, $rule));
            }

            if (isset($rule['properties'])) {
                // Validate nested properties even when customerData is missing/empty
                $nestedData = is_array($value) ? $value : [];
                $nestedErrors = self::validateProperties($nestedData, $rule['properties']);
                $errors = array_merge($errors, $nestedErrors);
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
    public static function clearOrderData(
        array $orderData
    ): array
    {
        foreach (self::FIELD_RULES as $field => $rule) {
            if ($rule['required'] === false && self::isEmptyForClear($orderData[$field] ?? null, $rule)) {
                unset($orderData[$field]);
            }

            if (isset($rule['properties']) && isset($orderData[$field]) && is_array($orderData[$field])) {
                foreach ($rule['properties'] as $subField => $subRule) {
                    if ($subRule['required'] === false && self::isEmptyForClear($orderData[$field][$subField] ?? null, $subRule)) {
                        unset($orderData[$field][$subField]);
                    }
                }
            }
        }

        return $orderData;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array $rule
     * @return array|array[]
     */
    private static function validateFieldValue(string $field, mixed $value, array $rule): array
    {
        $errors = [];

        $typeError = self::validateType($field, $value, $rule);
        if ($typeError !== null) {
            return [$typeError];
        }

        if (isset($rule['maxLength']) && is_string($value) && mb_strlen($value) > $rule['maxLength']) {
            $errors[] = [
                'field'   => $field,
                'message' => 'exceeds maxLength of ' . $rule['maxLength'],
            ];
        }

        if (isset($rule['maxDigits']) && (is_int($value) || is_numeric($value))) {
            $digits = strlen((string)(int)$value);
            if ($digits > $rule['maxDigits']) {
                $errors[] = [
                    'field'   => $field,
                    'message' => 'exceeds maxDigits of ' . $rule['maxDigits'],
                ];
            }
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
     * @param string $field
     * @param mixed $value
     * @param array $rule
     * @return string[]|null
     */
    private static function validateType(string $field, mixed $value, array $rule): ?array
    {
        $expectedTypes = $rule['types'] ?? [$rule['type'] ?? 'string'];

        foreach ($expectedTypes as $expectedType) {
            if (self::matchesType($value, $expectedType)) {
                return null;
            }

            if (
                $expectedType === 'int'
                && is_string($value)
                && is_numeric($value)
                && strpos($value, '.') === false
            ) {
                return null;
            }
        }

        return [
            'field' => $field,
            'message' => 'invalid type, expected ' . implode('|', $expectedTypes),
        ];
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
     * @return string[]|null
     */
    private static function validateFormat(string $field, mixed $value, string $format): ?array
    {
        return match ($format) {
            'iso3166-1-numeric' => (is_string($value) && preg_match('/^\\d{1,3}$/', $value))
                ? null
                : [
                    'field' => $field,
                    'message' => 'invalid format, expected ISO 3166-1 numeric country code (1-3 digits, e.g. "804")'
                ],
            'digits-only' => (is_string($value) && preg_match('/^\\d+$/', $value))
                ? null
                : [
                    'field' => $field,
                    'message' => 'invalid format, expected digits only without +, spaces or other characters (e.g. "380630000000")'
                ],
            'date-dmy' => (is_string($value) && preg_match('/^\\d{2}\\.\\d{2}\\.\\d{4}$/', $value))
                ? null
                : [
                    'field' => $field,
                    'message' => 'invalid format, expected date in d.m.Y format (e.g. "31.12.2000")'
                ],
            default => null,
        };
    }

    /**
     * @param mixed $value
     * @param array $rule
     * @return bool
     */
    private static function isEmptyForClear(mixed $value, array $rule): bool
    {
        $type = $rule['type'] ?? null;
        if ($type === 'boolean' && $value === false) {
            return true;
        }

        return self::isEmpty($value);
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
