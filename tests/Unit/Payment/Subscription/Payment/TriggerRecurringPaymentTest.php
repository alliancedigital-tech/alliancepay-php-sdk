<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Payment;

use AlliancePay\Sdk\Exceptions\TriggerRecurringPaymentException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Payment\RecurringPaymentTriggerRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Payment\RecurringPaymentTriggerResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Payment\TriggerRecurringPayment;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class TriggerRecurringPaymentTest extends TestCase
{
    /**
     * @return void
     * @throws TriggerRecurringPaymentException
     */
    public function test_trigger_recurring_payment_sends_required_fields_and_returns_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_RECURRING_SUBSCRIPTION_PAYMENT,
                $this->callback(fn (array $data) => $data['merchantId'] === 'M123'
                    && $data['scheduledPaymentId'] === 'SCHED-1'
                    && $data['date'] === '2026-10-15 12:00:00.000+02:00'
                    && !array_key_exists('hppOrderId', $data)),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getRecurringSubscriptionPaymentUrl')->willReturn('https://api.test.ua/recurring');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['operationId' => 'OP-1', 'ecomOperationId' => 'ECOM-OP-1', 'status' => 'SUCCESS'],
            'headers' => [],
        ]);

        $service = new TriggerRecurringPayment($httpClientMock);
        $dto = new RecurringPaymentTriggerRequestDTO('req-manual-1', 'M123', '2026-10-15 12:00:00.000+02:00', 'SCHED-1');

        $result = $service->triggerRecurringPayment($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(RecurringPaymentTriggerResponseDTO::class, $result);
        $this->assertSame('SUCCESS', $result->getStatus());
    }

    /**
     * @return void
     */
    public function test_trigger_recurring_payment_wraps_provider_error(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getRecurringSubscriptionPaymentUrl')->willReturn('https://api.test.ua/recurring');
        $httpClientMock->method('request')->willReturn([
            'code' => 400,
            'body' => ['msgType' => 'ERROR', 'msgCode' => 'E1', 'msgText' => 'Wrong scheduled date'],
            'headers' => [],
        ]);

        $service = new TriggerRecurringPayment($httpClientMock);
        $dto = new RecurringPaymentTriggerRequestDTO('req-manual-1', 'M123', '2026-10-15 12:00:00.000+02:00', 'SCHED-1');

        $this->expectException(TriggerRecurringPaymentException::class);

        $service->triggerRecurringPayment($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));
    }
}
