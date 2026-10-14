<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\SubscriptionOrder;

use AlliancePay\Sdk\Exceptions\CreateSubscriptionOrderException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder\SubscriptionOrderResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder\CreateSubscriptionOrder;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class CreateSubscriptionOrderTest extends TestCase
{
    /**
     * @return array
     */
    private function getOrderData(): array
    {
        return [
            'planId' => 'plan-1',
            'merchantId' => 'M123',
            'merchantRequestId' => 'req-1',
            'statusPageType' => 'standard',
            'successUrl' => 'https://ok.com',
            'failUrl' => 'https://fail.com',
            'hppPaymentMethods' => ['CARD'],
            'customerData' => ['senderCustomerId' => 'user-1'],
        ];
    }

    /**
     * @return void
     * @throws CreateSubscriptionOrderException
     * @throws ValidateDataException
     */
    public function test_create_subscription_order_sends_correct_request_and_returns_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_CREATE_SUBSCRIPTION_ORDER,
                $this->anything(),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getCreateSubscriptionOrderUrl')->willReturn('https://api.test.ua/sub-order');
        $httpClientMock->expects($this->once())->method('request')->willReturn([
            'code' => 200,
            'body' => ['hppOrderId' => 'HPP-SUB-1', 'redirectUrl' => 'https://redirect.url/pay/1'],
            'headers' => [],
        ]);

        $service = new CreateSubscriptionOrder($httpClientMock);
        $orderRequest = SubscriptionOrderRequestDTO::fromArray($this->getOrderData());

        $result = $service->createSubscriptionOrder($orderRequest, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(SubscriptionOrderResponseDTO::class, $result);
        $this->assertSame('HPP-SUB-1', $result->getHppOrderId());
    }

    /**
     * @return void
     */
    public function test_missing_customer_id_fails_validation(): void
    {
        $data = $this->getOrderData();
        unset($data['customerData']['senderCustomerId']);

        $this->expectException(ValidateDataException::class);

        SubscriptionOrderRequestDTO::fromArray($data);
    }
}
