<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Order;

use Exception;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Dto\Order\OrderDataResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;

class OrderDataResponseDTOTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function test_it_correctly_hydrates_nested_operations(): void
    {
        $data = [
            'coinAmount' => 1000,
            'ecomOrderId' => 'E-1',
            'statusUrl' => 'https://url.com',
            'merchantId' => 'M-1',
            'hppOrderId' => 'H-1',
            'redirectUrl' => 'https://url.com',
            'hppPayType' => 'CARD',
            'notificationUrl' => 'https://url.com',
            'merchantRequestId' => 'R-1',
            'orderStatus' => 'SUCCESS',
            'paymentMethods' => [],
            'expiredOrderDate' => '2020-12-31',
            'createDate' => '2020-12-31',
            'operations' => [
                [
                    'type' => OperationPurchaseDTO::OPERATION_TYPE,
                    'coinAmount' => 18000,
                    'merchantId' => 'testMerchantId',
                    'operationId' => 'testOperationId',
                    'ecomOperationId' => 'testEcomOperationId',
                    'status' => 'SUCCESS',
                    'transactionType' => 35,
                    'transactionCurrency' => '980',
                    'creationDateTime' => '2026.01.23 11:11:59.903',
                    'modificationDateTime' => '2026.01.23 11:12:00.174',
                    'transactionResponseInfo' => [],
                    'bankCode' => 'BANK_ALLIANCE',
                    'paymentSystem' => 'MasterCard',
                    'paymentServiceType' => 'CARD',
                    'notificationEncryption' => false,
                    'notificationSignature' => false,
                    'hppOrderId' => 'testHppOrderId',
                    'processingTerminalId' => 'testProcessingTerminalId',
                    'processingMerchantId' => 'testProcessingMerchantId',
                    'creatorSystem' => 'HPP',
                    'cardNumberMask' => '999999••••••9999',
                    'desiredThreeDSMode' => 'SHOULD',
                    'senderCustomerId' => 'testSenderCustomerId',
                    'senderFirstName' => 'testSenderFirstName',
                    'senderLastName' => 'testSenderLastName',
                    'senderEmail' => 'testSenderEmail@example.com',
                    'senderCountry' => '220',
                    'senderCity' => 'Test City',
                    'senderStreet' => 'Test Street',
                    'senderPhone' => '0999999999',
                    'senderZipCode' => '999999',
                    'senderBankCode' => 'BANK_ALLIANCE',
                    'senderPaymentSystem' => 'MasterCard',
                    'senderCardNumberMask' => '999999••••••9999'
                ]
            ]
        ];

        $dto = OrderDataResponseDTO::fromArray($data);

        $this->assertCount(1, $dto->getOperations());
        $this->assertInstanceOf(OperationPurchaseDTO::class, $dto->getOperations()[0]);
        $this->assertEquals(18000, $dto->getOperations()[0]->getCoinAmount());
    }

    /**
     * @param string $type
     * @return array
     */
    private function buildOperationData(string $type): array
    {
        return [
            'type' => $type,
            'coinAmount' => 5000,
            'merchantId' => 'testMerchantId',
            'operationId' => 'testOperationId',
            'ecomOperationId' => 'testEcomOperationId',
            'status' => 'SUCCESS',
            'transactionType' => 196,
            'transactionCurrency' => '980',
            'creationDateTime' => '2026.01.23 11:11:59.903',
            'modificationDateTime' => '2026.01.23 11:12:00.174',
            'transactionResponseInfo' => [],
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'hppOrderId' => 'testHppOrderId',
        ];
    }

    /**
     * @param array $operations
     * @return array
     */
    private function buildOrderData(array $operations): array
    {
        return [
            'coinAmount' => 5000,
            'ecomOrderId' => 'E-1',
            'statusUrl' => null,
            'merchantId' => 'M-1',
            'hppOrderId' => 'H-1',
            'redirectUrl' => null,
            'hppPayType' => 'PREAUTH',
            'notificationUrl' => null,
            'merchantRequestId' => 'R-1',
            'orderStatus' => 'PENDING',
            'paymentMethods' => [],
            'expiredOrderDate' => '2026-12-31',
            'createDate' => '2026-07-30',
            'operations' => $operations,
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_it_hydrates_preauth_operation_as_operation_purchase_dto(): void
    {
        $data = $this->buildOrderData([
            $this->buildOperationData(OperationPurchaseDTO::OPERATION_TYPE_PREAUTH),
        ]);

        $dto = OrderDataResponseDTO::fromArray($data);

        $this->assertCount(1, $dto->getOperations());
        $this->assertInstanceOf(OperationPurchaseDTO::class, $dto->getOperations()[0]);
        $this->assertSame(OperationPurchaseDTO::OPERATION_TYPE_PREAUTH, $dto->getOperations()[0]->getType());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_it_hydrates_completion_operation_as_operation_purchase_dto(): void
    {
        $data = $this->buildOrderData([
            $this->buildOperationData(OperationPurchaseDTO::OPERATION_TYPE_COMPLETION),
        ]);

        $dto = OrderDataResponseDTO::fromArray($data);

        $this->assertCount(1, $dto->getOperations());
        $this->assertInstanceOf(OperationPurchaseDTO::class, $dto->getOperations()[0]);
        $this->assertSame(OperationPurchaseDTO::OPERATION_TYPE_COMPLETION, $dto->getOperations()[0]->getType());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_it_hydrates_mixed_preauth_and_completion_operations(): void
    {
        $data = $this->buildOrderData([
            $this->buildOperationData(OperationPurchaseDTO::OPERATION_TYPE_PREAUTH),
            $this->buildOperationData(OperationPurchaseDTO::OPERATION_TYPE_COMPLETION),
        ]);

        $dto = OrderDataResponseDTO::fromArray($data);
        $operations = $dto->getOperations();

        $this->assertCount(2, $operations);
        $this->assertInstanceOf(OperationPurchaseDTO::class, $operations[0]);
        $this->assertInstanceOf(OperationPurchaseDTO::class, $operations[1]);
        $this->assertSame(OperationPurchaseDTO::OPERATION_TYPE_PREAUTH, $operations[0]->getType());
        $this->assertSame(OperationPurchaseDTO::OPERATION_TYPE_COMPLETION, $operations[1]->getType());
    }
}
