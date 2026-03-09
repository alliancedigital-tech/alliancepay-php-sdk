<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Order;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Order\OrderResponseDTO;

class OrderDtoTest extends TestCase
{
    public function test_order_request_dto_mapping(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-123',
            'merchantId' => 'M-777',
            'hppPayType' => 'PURCHASE',
            'coinAmount' => 15050,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://site.com/success',
            'failUrl' => 'https://site.com/fail',
            'statusPageType' => 'REDIRECT',
            'customerData' => ['senderCustomerId' => 'id_1'],
            'merchantComment' => 'Test Order'
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertEquals('REQ-123', $dto->getMerchantRequestId());
        $this->assertEquals(15050, $dto->getCoinAmount());
        $this->assertArrayHasKey('senderCustomerId', $dto->toArray()['customerData']);
    }

    public function test_order_response_dto_dates(): void
    {
        $data = [
            'hppOrderId' => 'HPP-1',
            'merchantRequestId' => 'REQ-1',
            'hppPayType' => 'CARD',
            'paymentMethods' => ['CARD'],
            'orderStatus' => 'PENDING',
            'coinAmount' => 100,
            'merchantId' => 'M1',
            'expiredOrderDate' => '2026.02.20 12:00:00.000',
            'redirectUrl' => 'https://pay.ua',
            'createDate' => '2026.02.16 10:00:00.000',
            'statusUrl' => 'https://status.ua'
        ];

        $dto = OrderResponseDTO::fromArray($data);

        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getCreateDate());
        $this->assertEquals('2026-02-16', $dto->getCreateDate()->format('Y-m-d'));
    }
}
