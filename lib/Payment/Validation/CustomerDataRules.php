<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Validation;

/**
 * Class CustomerDataRules.
 *
 * Shared `customerData.*` field rules, used by {@see \AlliancePay\Sdk\Payment\Order\Validation\ValidateOrderData}
 * and {@see \AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder\Validation\ValidateSubscriptionOrderData} —
 * both payloads carry the same customer fields.
 */
final class CustomerDataRules
{
    public const PROPERTIES = [
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
            'type' => 'string',
            'maxLength' => 3,
            'format' => 'iso3166-1-numeric',
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
            'type' => 'string',
            'maxLength' => 20,
            'format' => 'digits-only',
            'required' => false,
        ],
        'senderBirthday' => [
            'type' => 'string',
            'maxLength' => 50,
            'format' => 'date-dmy',
            'required' => false,
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
    ];
}
