<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Order\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Order\Validation\ValidateOrderData;

/**
 * Class ValidateOrderDataTest.
 */
class ValidateOrderDataTest extends TestCase
{
    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_throws_exception_if_required_field_missing(): void
    {
        $invalidData = [
            'merchantRequestId' => 'req_123'
        ];

        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage(
            'Order data field merchantId, hppPayType, coinAmount, '
            . 'paymentMethods, successUrl, failUrl, statusPageType, senderCustomerId cannot be empty.'
        );

        ValidateOrderData::validateOrderRequiredData($invalidData);
    }

    /**
     * @return void
     */
    public function test_it_clears_empty_additional_fields(): void
    {
        $data = [
            'merchantRequestId' => 'req_123',
            'merchantId' => 'M123',
            'hppPayType' => 'PURCHASE',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/s',
            'failUrl' => 'https://example.com/f',
            'statusPageType' => 'standard',
            'merchantComment' => '',
            'customerData' => [
                'senderCustomerId' => 'cust_1',
                'senderFirstName' => ''
            ]
        ];

        $result = ValidateOrderData::clearOrderData($data);

        $this->assertArrayNotHasKey('merchantComment', $result);
        $this->assertArrayNotHasKey('senderFirstName', $result['customerData']);
        $this->assertArrayHasKey('merchantId', $result);
    }
}
