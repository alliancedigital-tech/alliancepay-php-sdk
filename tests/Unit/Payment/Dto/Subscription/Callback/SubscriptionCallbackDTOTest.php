<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Dto\Subscription\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Callback\SubscriptionCallbackDTO;
use PHPUnit\Framework\TestCase;

class SubscriptionCallbackDTOTest extends TestCase
{
    /**
     * @return array
     */
    private function getOperationData(): array
    {
        return [
            'type' => OperationPurchaseDTO::OPERATION_TYPE,
            'coinAmount' => 10000,
            'operationId' => 'op-1',
            'ecomOperationId' => 'ecom-op-1',
            'status' => 'SUCCESS',
            'transactionCurrency' => '980',
            'creationDateTime' => '2026.01.23 11:11:59.903',
            'modificationDateTime' => '2026.01.23 11:12:00.174',
            'transactionResponseInfo' => [],
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'hppOrderId' => 'HPP-1',
        ];
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_it_builds_without_operation_when_absent(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'PENDING',
            'scheduledPayments' => [],
        ]);

        $this->assertNull($dto->getOperation());
        $this->assertSame('HPP-1', $dto->getHppOrderId());
    }

    /**
     * @return void
     */
    public function test_it_throws_when_hpp_order_id_is_missing(): void
    {
        $this->expectException(CallbackException::class);

        SubscriptionCallbackDTO::fromArray(['orderStatus' => 'SUCCESS']);
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_it_resolves_operation_from_operations_array_when_operation_object_absent(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'SUCCESS',
            'operations' => [$this->getOperationData()],
        ]);

        $this->assertNotNull($dto->getOperation());
        $this->assertSame('op-1', $dto->getOperation()->getOperationId());
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_is_initial_payment_true_when_recurring_transaction_type_absent(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'SUCCESS',
        ]);

        $this->assertTrue($dto->isInitialPayment());
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_is_initial_payment_false_when_recurring_transaction_type_present(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'SUCCESS',
            'recurringTransactionType' => 'RECURRING',
        ]);

        $this->assertFalse($dto->isInitialPayment());
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_get_raw_payload_distinguishes_absent_from_explicit_null(): void
    {
        $data = [
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'SUCCESS',
            'scheduledPaymentId' => null,
        ];

        $dto = SubscriptionCallbackDTO::fromArray($data);
        $raw = $dto->getRawPayload();

        $this->assertArrayHasKey('scheduledPaymentId', $raw);
        $this->assertNull($raw['scheduledPaymentId']);
        $this->assertArrayNotHasKey('ecomOrderId', $raw);
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_reconcile_amount_returns_null_when_no_operation_present(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'PENDING',
        ]);

        $this->assertNull($dto->reconcileAmount(10000));
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_reconcile_amount_returns_null_on_match(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'SUCCESS',
            'operation' => $this->getOperationData(),
        ]);

        $this->assertNull($dto->reconcileAmount(10000));
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_reconcile_amount_returns_warning_string_not_exception_on_mismatch(): void
    {
        $dto = SubscriptionCallbackDTO::fromArray([
            'hppOrderId' => 'HPP-1',
            'orderStatus' => 'SUCCESS',
            'operation' => $this->getOperationData(),
        ]);

        $warning = $dto->reconcileAmount(99999);

        $this->assertIsString($warning);
        $this->assertStringContainsString('HPP-1', $warning);
    }
}
