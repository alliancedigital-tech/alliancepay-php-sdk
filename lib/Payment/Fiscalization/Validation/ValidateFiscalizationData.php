<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Fiscalization\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;

/**
 * Class ValidateFiscalizationData.
 */
class ValidateFiscalizationData
{
    public const REQUIRED_REQUEST_FIELDS = [
        'merchantRequestId',
        'merchantId',
        'operationId',
        'notificationUrl',
        'products',
    ];

    public const REQUIRED_PRODUCT_FIELDS = [
        'id',
        'name',
        'count',
        'measurementCode',
        'unitPrice',
    ];

    /** taxCode values that require classificationCode (and excise labels for excisable goods). */
    public const CLASSIFICATION_REQUIRED_TAX_CODES = [2, 7, 8];

    /**
     * @param array $data
     * @return array
     * @throws ValidateDataException
     */
    public static function validateRequestData(array $data): array
    {
        $errors = [];

        foreach (self::REQUIRED_REQUEST_FIELDS as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === []) {
                $errors[] = 'Fiscalization field ' . $field . ' cannot be empty.';
            }
        }

        if (!empty($data['notificationUrl'])
            && (!is_string($data['notificationUrl']) || !self::isAbsoluteUrl($data['notificationUrl']))
        ) {
            $errors[] = 'Fiscalization field notificationUrl must be an absolute URL.';
        }

        if (isset($data['products']) && is_array($data['products'])) {
            foreach (array_values($data['products']) as $index => $product) {
                if (!is_array($product)) {
                    $errors[] = sprintf('products[%d] must be an object.', $index);
                    continue;
                }

                foreach (self::validateProductData($product, $index) as $error) {
                    $errors[] = $error;
                }
            }
        } elseif (isset($data['products'])) {
            $errors[] = 'Fiscalization field products must be an array.';
        }

        if ($errors !== []) {
            throw new ValidateDataException(implode(' ', $errors));
        }

        return $data;
    }

    /**
     * @param array $product
     * @param int $index
     * @return string[] List of validation errors (empty when the product is valid).
     */
    public static function validateProductData(array $product, int $index = 0): array
    {
        $errors = [];
        $prefix = sprintf('products[%d].', $index);

        foreach (self::REQUIRED_PRODUCT_FIELDS as $field) {
            if (!isset($product[$field]) || $product[$field] === '') {
                $errors[] = $prefix . $field . ' cannot be empty.';
            }
        }

        if (isset($product['count']) && (!is_numeric($product['count']) || (float)$product['count'] <= 0)) {
            $errors[] = $prefix . 'count must be a positive number.';
        }

        if (isset($product['unitPrice']) && (!is_numeric($product['unitPrice']) || (float)$product['unitPrice'] < 0)) {
            $errors[] = $prefix . 'unitPrice must be a non-negative number (in UAH).';
        }

        $taxCode = $product['taxCode'] ?? null;

        if ($taxCode !== null && $taxCode !== '' && !is_numeric($taxCode)) {
            $errors[] = $prefix . 'taxCode must be an integer.';
        }

        if (is_numeric($taxCode) && in_array((int)$taxCode, self::CLASSIFICATION_REQUIRED_TAX_CODES, true)) {
            if (empty($product['classificationCode'])) {
                $errors[] = $prefix . 'classificationCode is required when taxCode is 2, 7 or 8.';
            } elseif (!ctype_digit((string)$product['classificationCode'])) {
                $errors[] = $prefix . 'classificationCode must contain digits only.';
            }
        }

        return $errors;
    }

    /**
     * @param string $url
     * @return bool
     */
    private static function isAbsoluteUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }
}
