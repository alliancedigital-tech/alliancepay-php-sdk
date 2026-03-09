<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Order\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Refund\Validation\ValidateRefundData;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidateRefundDataTest.
 */
class ValidateRefundDataTest extends TestCase
{
    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_throws_exception_if_required_field_missing(): void
    {
        $invalidData = [
            'merchantRequestId' => 'REQ-1'
        ];

        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage(
            'Refund data field merchantId, operationId, coinAmount, date cannot be empty.'
        );

        ValidateRefundData::validateRefundRequiredData($invalidData);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_clears_empty_additional_fields(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-1',
            'merchantId' => 'merchantId',
            'operationId' => 'operationId',
            'coinAmount' => 100,
            'date' => '2026-01-01 09:00:00',
            'merchantComment' => ''
        ];

        $result = ValidateRefundData::clearRefundData($data);
        $this->assertArrayNotHasKey('merchantComment', $result);
        $this->assertArrayHasKey('merchantId', $result);
    }
}
