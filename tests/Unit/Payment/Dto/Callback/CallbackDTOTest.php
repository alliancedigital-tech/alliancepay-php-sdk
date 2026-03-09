<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Callback\CallbackDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;
use PHPUnit\Framework\TestCase;

class CallbackDTOTest extends TestCase
{
    /**
     * @return array
     */
    private function getBaseCallbackData(): array
    {
        return [
            'ecomOrderId' => 'ECOM-12345',
            'coinAmount' => 1000,
            'merchantId' => 'MERCH-777',
            'statusUrl' => 'https://site.com/status',
            'redirectUrl' => 'https://site.com/redirect',
            'notificationUrl' => 'https://site.com/callback',
            'notificationEncryption' => true,
            'hppOrderId' => 'HPP-1001',
            'hppDirectType' => 'CARD',
            'merchantRequestId' => 'REQ-001',
            'createDate' => '2026.02.13 10:06:01.927',
            'paymentMethods' => ['CARD', 'APPLE_PAY', 'GOOGLE_PAY'],
            'orderStatus' => 'SUCCESS',
            'expiredOrderDate' => '2026.02.14 10:06:01.927',
        ];
    }

    private  function getPurchaseOperationData(): array
    {
        return [
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
        ];
    }

    private  function getRefundOperationData(): array
    {
        return [
            'type' => OperationRefundDTO::OPERATION_TYPE,
            'rrn' => 'testRrn',
            'coinAmount' => 6800,
            'merchantId' => 'testMerchantId',
            'operationId' => 'testOperationId',
            'ecomOperationId' => 'testEcomOperationId',
            'merchantName' => 'Test MerchantName',
            'approvalCode' => 'testApprovalCode',
            'status' => 'SUCCESS',
            'transactionType' => 76,
            'merchantRequestId' => 'testMerchantRequestId',
            'transactionCurrency' => '980',
            'creationDateTime' => '2026.02.12 14:24:54.347',
            'modificationDateTime' => '2026.02.12 14:25:56.238',
            'processingDateTime' => '2026.02.12 14:24:54.000',
            'transactionResponseInfo' => [
                'actionCode' => '0',
                'responseCode' => '00'
            ],
            'bankCode' => 'BANK_ALLIANCE',
            'paymentSystem' => 'MasterCard',
            'productType' => 'PURCHASE',
            'notificationUrl' => 'https://site.com/callback',
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'hppOrderId' => 'testHppOrderId',
            'processingTerminalId' => 'testProcessingTerminalId',
            'processingMerchantId' => 'testProcessingMerchantId',
            'creatorSystem' => 'HPP',
            'rrnOriginal' => 'testRrn',
            'originalOperationId' => 'testOriginalOperationId',
            'originalCoinAmount' => 6800,
            'cardNumberMask' => '999999••••••9999',
            'originalEcomOperationId' => 'testOriginalEcomOperationId',
        ];
    }

    /**
     * @return void
     */
    public function test_from_array_creates_purchase_operation(): void
    {
        $data = $this->getBaseCallbackData();
        $data['operation'] = $this->getPurchaseOperationData();
        $dto = CallbackDTO::fromArray($data);

        $this->assertInstanceOf(CallbackDTO::class, $dto);
        $this->assertInstanceOf(OperationPurchaseDTO::class, $dto->toArray()['operation']);
        $this->assertSame('ECOM-12345', $data['ecomOrderId']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->toArray()['createDate']);
    }

    /**
     * @return void
     */
    public function test_from_array_creates_refund_operation(): void
    {
        $data = $this->getBaseCallbackData();
        $data['operation'] = $this->getRefundOperationData();

        $dto = CallbackDTO::fromArray($data);
        $this->assertInstanceOf(CallbackDTO::class, $dto);
        $this->assertInstanceOf(OperationRefundDTO::class, $dto->toArray()['operation']);
        $this->assertSame('ECOM-12345', $data['ecomOrderId']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->toArray()['createDate']);
    }

    /**
     * @return void
     */
    public function test_from_array_throws_exception_on_unknown_operation_type(): void
    {
        $data = $this->getBaseCallbackData();
        $data['operation'] = $this->getPurchaseOperationData();
        $data['operation']['type'] = 'UNKNOWN_TYPE';

        $this->expectException(CallbackException::class);
        $this->expectExceptionMessage('Unknown operation type: UNKNOWN_TYPE');

        CallbackDTO::fromArray($data);
    }

    /**
     * @return void
     */
    public function test_to_array_returns_correct_structure(): void
    {
        $data = $this->getBaseCallbackData();
        $data['operation'] = $this->getPurchaseOperationData();

        $dto = CallbackDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertArrayHasKey('ecomOrderId', $result);
        $this->assertArrayHasKey('operation', $result);
        $this->assertTrue($result['notificationEncryption']);
        $this->assertEquals('HPP-1001', $result['hppPayType']);
    }
}
