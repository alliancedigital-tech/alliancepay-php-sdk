<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Order\Validation;

use AlliancePay\Sdk\Exceptions\CreateOrderException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;

/**
 * Class ValidateOrderData.
 */
class ValidateOrderData
{
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

    public const REQUIRED_CUSTOMER_DATA_FIELD = 'customerData';
    public const REQUIRED_CUSTOMER_DATA_FIELD_SENDER_CUSTOMER_ID = 'senderCustomerId';
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

    /**
     * @param array $orderData
     * @return array
     * @throws ValidateDataException
     */
    public static function validateOrderRequiredData(
        array $orderData
    ): array {
        $errorFields = [];

        foreach (self::REQUIRED_CRETE_ORDER_DATA_FIELDS as $field) {
            if (empty($orderData[$field])) {
                $errorFields[] = $field;
            }
        }

        if (empty(
            $orderData[self::REQUIRED_CUSTOMER_DATA_FIELD][self::REQUIRED_CUSTOMER_DATA_FIELD_SENDER_CUSTOMER_ID]
            )
        ) {
            $errorFields[] = self::REQUIRED_CUSTOMER_DATA_FIELD_SENDER_CUSTOMER_ID;
        }

        if (!empty($errorFields)) {
            throw new ValidateDataException(
                'Order data field ' . implode(', ', $errorFields) . ' cannot be empty.'
            );
        }

        return $orderData ?? [];
    }

    /**
     * @param array $orderData
     * @return array
     */
    public static function clearOrderData(
        array $orderData
    ): array {

        foreach (self::ADDITIONAL_CREATE_ORDER_DATA_FIELDS as $field) {
            if (empty($orderData[$field])) {
                unset($orderData[$field]);
            }
        }

        foreach (self::ADDITIONAL_CUSTOMER_DATA_FIELDS as $field) {
            if (empty($orderData[self::REQUIRED_CUSTOMER_DATA_FIELD][$field])) {
                unset($orderData[self::REQUIRED_CUSTOMER_DATA_FIELD][$field]);
            }
        }

        return $orderData;
    }
}
