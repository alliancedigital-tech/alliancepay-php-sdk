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
            'merchantRequestId'    => 'REQ-123',
            'merchantId'           => 'M-777',
            'hppPayType'           => 'PURCHASE',
            'directType'           => 'REDIRECT',
            'coinAmount'           => 15050,
            'paymentMethods'       => ['CARD'],
            'successUrl'           => 'https://site.com/success',
            'failUrl'              => 'https://site.com/fail',
            'statusPageType'       => 'REDIRECT',
            'customerData'         => ['senderCustomerId' => 'id_1'],
            'merchantComment'      => 'Test Order',
            'hppPageAdditionalInfo' => [
                'productsSum' => 5000,
                'products'    => [
                    ['name' => 'Widget', 'count' => 2, 'sum' => 2500],
                    ['name' => 'Gadget', 'count' => 1, 'sum' => 2500],
                ],
            ],
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertEquals('REQ-123', $dto->getMerchantRequestId());
        $this->assertEquals(15050, $dto->getCoinAmount());
        $this->assertArrayHasKey('senderCustomerId', $dto->toArray()['customerData']);
        $this->assertSame(5000, $dto->getHppPageAdditionalInfo()['productsSum']);
        $this->assertCount(2, $dto->getHppPageAdditionalInfo()['products']);
    }

    public function test_order_request_dto_to_array_includes_hpp_page_additional_info(): void
    {
        $hppInfo = [
            'productsSum' => 3000,
            'products'    => [
                ['name' => 'Item', 'count' => 3, 'sum' => 1000],
            ],
        ];

        $data = [
            'merchantRequestId'    => 'REQ-456',
            'merchantId'           => 'M-888',
            'hppPayType'           => 'PURCHASE',
            'directType'           => 'REDIRECT',
            'coinAmount'           => 3000,
            'paymentMethods'       => ['CARD'],
            'successUrl'           => 'https://site.com/success',
            'failUrl'              => 'https://site.com/fail',
            'statusPageType'       => 'REDIRECT',
            'customerData'         => ['senderCustomerId' => 'id_2'],
            'hppPageAdditionalInfo' => $hppInfo,
        ];

        $dto    = OrderRequestDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertArrayHasKey('hppPageAdditionalInfo', $result);
        $this->assertSame(3000, $result['hppPageAdditionalInfo']['productsSum']);
        $this->assertSame('Item', $result['hppPageAdditionalInfo']['products'][0]['name']);
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
