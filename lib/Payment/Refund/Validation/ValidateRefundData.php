<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Refund\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;

/**
 * Class ValidateRefundData.
 */
class ValidateRefundData
{
    public const REQUIRED_REFUND_ORDER_DATA_FIELDS = [
        'merchantRequestId',
        'merchantId',
        'operationId',
        'coinAmount',
        'date'
    ];

    public const ADDITIONAL_REFUND_ORDER_DATA_FIELDS = [
        'notificationUrl',
        'notificationEncryption',
        'merchantComment'
    ];

    /**
     * @param array $refundData
     * @return array
     * @throws ValidateDataException
     */
    public static function validateRefundRequiredData(
        array $refundData
    ): array {
        $errorFields = [];

        foreach (self::REQUIRED_REFUND_ORDER_DATA_FIELDS as $field) {
            if (empty($refundData[$field])) {
                $errorFields[] = $field;
            }
        }

        if (!empty($errorFields)) {
            throw new ValidateDataException(
                'Refund data field '
                . implode(', ', $errorFields)
                . ' cannot be empty.'
            );
        }

        return $refundData ?? [];
    }

    /**
     * @param array $refundData
     * @return array
     */
    public static function clearRefundData(array $refundData): array
    {
        foreach (self::ADDITIONAL_REFUND_ORDER_DATA_FIELDS as $field) {
            if (empty($refundData[$field])) {
                unset($refundData[$field]);
            }
        }

        return $refundData ?? [];
    }
}
