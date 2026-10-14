<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\SubscriptionOrder;

use AlliancePay\Sdk\Exceptions\CancelSubscriptionOrderException;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderCancelRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderCancelResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder\CancelSubscriptionOrder;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class CancelSubscriptionOrderTest extends TestCase
{
    /**
     * @return void
     * @throws CancelSubscriptionOrderException
     */
    public function test_cancel_subscription_order_resolves_by_hpp_order_id(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_CANCEL_SUBSCRIPTION_ORDER,
                $this->callback(fn (array $data) => $data['hppOrderId'] === 'HPP-SUB-1'),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getCancelSubscriptionOrderUrl')->willReturn('https://api.test.ua/sub-order/cancel');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['hppOrderId' => 'HPP-SUB-1', 'status' => 'CANCELED'],
            'headers' => [],
        ]);

        $service = new CancelSubscriptionOrder($httpClientMock);
        $dto = new SubscriptionOrderCancelRequestDTO('HPP-SUB-1', 'M123');

        $result = $service->cancelSubscriptionOrder($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(SubscriptionOrderCancelResponseDTO::class, $result);
        $this->assertSame('CANCELED', $result->getStatus());
    }
}
