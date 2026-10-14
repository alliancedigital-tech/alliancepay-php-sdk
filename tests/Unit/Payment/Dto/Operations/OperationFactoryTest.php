<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Dto\Operations;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationFactory;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;
use PHPUnit\Framework\TestCase;

class OperationFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws CallbackException
     */
    public function test_it_dispatches_purchase_type_to_purchase_dto(): void
    {
        $operation = OperationFactory::fromArray([
            'type' => OperationPurchaseDTO::OPERATION_TYPE,
            'creationDateTime' => '2026.01.23 11:11:59.903',
            'modificationDateTime' => '2026.01.23 11:12:00.174',
            'transactionResponseInfo' => [],
            'notificationEncryption' => false,
            'notificationSignature' => false,
        ]);

        $this->assertInstanceOf(OperationPurchaseDTO::class, $operation);
    }

    /**
     * @return void
     * @throws CallbackException
     */
    public function test_it_dispatches_refund_type_to_refund_dto(): void
    {
        $operation = OperationFactory::fromArray([
            'type' => OperationRefundDTO::OPERATION_TYPE,
            'creationDateTime' => '2026.01.23 11:11:59.903',
            'modificationDateTime' => '2026.01.23 11:12:00.174',
            'transactionResponseInfo' => [],
            'notificationEncryption' => false,
            'notificationSignature' => false,
        ]);

        $this->assertInstanceOf(OperationRefundDTO::class, $operation);
    }

    /**
     * @return void
     */
    public function test_it_throws_on_unknown_type(): void
    {
        $this->expectException(CallbackException::class);
        $this->expectExceptionMessage('Unknown operation type: SOMETHING_ELSE');

        OperationFactory::fromArray(['type' => 'SOMETHING_ELSE']);
    }

    /**
     * @return void
     */
    public function test_it_throws_when_type_is_missing(): void
    {
        $this->expectException(CallbackException::class);
        $this->expectExceptionMessage('Operation type is missing');

        OperationFactory::fromArray([]);
    }
}
