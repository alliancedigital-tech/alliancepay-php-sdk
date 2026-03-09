<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Refund;

use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundResponseDTO;

class RefundDtoTest extends TestCase
{
    public function test_refund_request_serialization(): void
    {
        $date = new DateTimeImmutable('2026-02-13 10:00:00');
        $dto = new RefundRequestDTO(
            'REQ-REF-1',
            'M-1',
            'OP-1',
            500,
            $date,
            'https://callback.ua'
        );

        $array = $dto->toArray();
        $this->assertEquals(500, $array['coinAmount']);
        $this->assertStringContainsString('2026-02-13', $array['date']);
    }

    public function test_refund_response_mapping(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-REF-1',
            'type' => OperationRefundDTO::OPERATION_TYPE,
            'rrn' => '123456789012',
            'coinAmount' => 500,
            'originalCoinAmount' => 500,
            'merchantId' => 'merchantId',
            'hppOrderId' => 'HPP_ORDER_ID_1',
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'originalOperationId' => 'originalOperationId',
            'operationId' => 'OP-1',
            'ecomOperationId' => 'ecomOperationId',
            'originalEcomOperationId' => 'originalEcomOperationId',
            'processingTerminalId' => 'processingTerminalId',
            'processingMerchantId' => 'processingMerchantId',
            'creatorSystem' => 'HPP',
            'status' => 'SUCCESS',
            'transactionType' => 76,
            'transactionCurrency' => '980',
            'creationDateTime' => '2026-02-13 10:00:00',
            'modificationDateTime' => '2026-02-13 10:00:00',
            'transactionResponseInfo' => [],
            'productType' => 'PURCHASE',
            'notificationUrl' => 'https://callback.ua',
        ];

        $dto = RefundResponseDTO::createFromArray($data);

        $this->assertEquals('SUCCESS', $dto->getStatus());
        $this->assertEquals('123456789012', $dto->getRrn());
    }
}
