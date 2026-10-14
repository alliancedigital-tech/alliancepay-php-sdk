<?php

/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Refund;

use AlliancePay\Sdk\Exceptions\AuthenticationException;
use AlliancePay\Sdk\Exceptions\RefundOrderException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundResponseDTO;
use AlliancePay\Sdk\Payment\Refund\RefundOrder;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

/**
 * Class RefundOrderTest.
 */
class RefundOrderTest extends TestCase
{
    private function getFakeRefundData(): array
    {
        return [
            'merchantRequestId' => 'REQ-REF-1',
            'type' => OperationRefundDTO::OPERATION_TYPE,
            'rrn' => '123456789012',
            'coinAmount' => 500,
            'originalCoinAmount' => 500,
            'merchantId' => 'merchantId',
            'hppOrderId' => 'HPP_ORDER_ID_1',
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'originalOperationId' => 'originalOperationId',
            'operationId' => 'OP-1',
            'ecomOperationId' => 'ecomOperationId',
            'originalEcomOperationId' => 'originalEcomOperationId',
            'processingTerminalId' => 'processingTerminalId',
            'processingMerchantId' => 'processingMerchantId',
            'creatorSystem' => 'HPP',
            'status' => 'SUCCESS',
            'transactionType' => 76,
            'transactionCurrency' => '980',
            'creationDateTime' => '2026-02-13 10:00:00',
            'modificationDateTime' => '2026-02-13 10:00:00',
            'transactionResponseInfo' => [],
            'productType' => 'PURCHASE',
            'notificationUrl' => 'https://callback.ua',
        ];
    }

    /**
     * @return void
     * @throws AuthenticationException
     * @throws RefundOrderException
     */
    public function test_create_refund_sends_request_and_returns_response_dto(): void
    {
        $auth = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);

        $refundRequest = new RefundRequestDTO(
            merchantRequestId: 'refund_req_123',
            merchantId: 'M123',
            operationId: 'orig_op_456',
            coinAmount: 1000, // 10.00 грн
            date: new DateTimeImmutable()
        );

        $httpClientMock = $this->createMock(HttpClientInterface::class);

        $httpClientMock->method('getRefundUrl')
            ->willReturn('https://api.test.ua/refund');

        $httpClientMock->method('buildOptionsFromAuthDto')
            ->willReturn(['headers' => [], 'body' => 'jws-token']);

        $mockedApiResponse = [
            'code' => 200,
            'body' => $this->getFakeRefundData(),
            'headers' => [],
        ];

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                HttpClientInterface::METHOD_POST,
                'https://api.test.ua/refund'
            )
            ->willReturn($mockedApiResponse);

        $refundService = new RefundOrder($httpClientMock);
        $result = $refundService->createRefund($refundRequest, $auth);

        $this->assertInstanceOf(RefundResponseDTO::class, $result);
    }

    /**
     * @return void
     * @throws AuthenticationException
     * @throws RefundOrderException
     */
    public function test_create_refund_wraps_business_error_response(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getRefundUrl')->willReturn('https://api.test.ua/refund');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['msgType' => 'ERROR', 'msgCode' => 'E1', 'msgText' => 'Refund rejected'],
        ]);

        $refundService = new RefundOrder($httpClientMock);

        $this->expectException(RefundOrderException::class);

        $auth = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'm',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);
        $refundRequest = new RefundRequestDTO(
            merchantRequestId: 'refund_req_123',
            merchantId: 'M123',
            operationId: 'orig_op_456',
            coinAmount: 1000,
            date: new DateTimeImmutable()
        );

        $refundService->createRefund($refundRequest, $auth);
    }
}
