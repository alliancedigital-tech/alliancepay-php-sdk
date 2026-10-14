<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Callback\SubscriptionCallbackDTO;
use AlliancePay\Sdk\Payment\Subscription\Callback\SubscriptionCallbackHandler;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use PHPUnit\Framework\TestCase;

class SubscriptionCallbackHandlerTest extends TestCase
{
    /**
     * @return void
     * @throws CallbackException
     */
    public function test_handle_with_explicit_payload_resolves_by_hpp_order_id(): void
    {
        $authDto = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);

        $handler = new SubscriptionCallbackHandler();
        $dto = $handler->handle($authDto, [
            'hppOrderId' => 'HPP-SUB-1',
            'orderStatus' => 'PENDING',
            'recurringTransactionType' => null,
            'scheduledPayments' => [],
        ]);

        $this->assertInstanceOf(SubscriptionCallbackDTO::class, $dto);
        $this->assertSame('HPP-SUB-1', $dto->getHppOrderId());
        $this->assertTrue($dto->isInitialPayment());
    }

    /**
     * @return void
     */
    public function test_handle_throws_when_hpp_order_id_missing_from_explicit_payload(): void
    {
        $authDto = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);

        $handler = new SubscriptionCallbackHandler();

        $this->expectException(CallbackException::class);

        $handler->handle($authDto, ['orderStatus' => 'PENDING']);
    }
}
