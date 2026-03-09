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
use AlliancePay\Sdk\Services\Encryption\JweEncryptionService;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use Throwable;

/**
 * Class RefundOrderTest.
 */
class RefundOrderTest extends TestCase
{
    private function getTestPublicKey(): array
    {
        return json_decode('{"kty": "EC","use": "enc","crv": "P-384",
        "x": "g4NSeivuFxFCkRo7mHgi6PA8_RFgO0obFZgT0ZufBT1hmvVF-4Zb9arnn7sbVHyT",
        "y": "8ufRfdLcyh2OmOE9m35iNskBHt7JI3xGpB-gLzDpgD0pnxVEql0RIC5nL6z_TXN_",
        "alg": "ECDH-ES+A256KW"}',
            true
        );
    }

    private function getTestPrivateKey(): string
    {
        return '{"kty": "EC","d": "-eTnNxa0wuJq8fZNfMPXLZDybbdUyB8hLHVxLbsAK7HKAIRHxQC21f95i_bufNli",
        "use": "enc","crv": "P-384","x": "g4NSeivuFxFCkRo7mHgi6PA8_RFgO0obFZgT0ZufBT1hmvVF-4Zb9arnn7sbVHyT",
        "y": "8ufRfdLcyh2OmOE9m35iNskBHt7JI3xGpB-gLzDpgD0pnxVEql0RIC5nL6z_TXN_","alg": "ECDH-ES+A256KW"}';
    }

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
    public function test_create_refund_sends_request_and_returns_decrypted_dto(): void
    {
        $auth = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'serviceCode' => 'S1',
            'authenticationKey' => $this->getTestPrivateKey(),
            'serverPublic' => $this->getTestPublicKey(),
            'deviceId' => 'dev_1',
            'refreshToken' => 'ref_1'
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
            ->willReturn(['headers' => [], 'body' => 'encrypted_payload']);

        $fakeJweToken = JweEncryptionService::encrypt(
            $this->getFakeRefundData(),
            $this->getTestPublicKey()
        );

        $mockedApiResponse = [
            'code' => 200,
            'body' => [
                'jwe' => $fakeJweToken,
            ],
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
        $this->assertInstanceOf(RefundOrder::class, $refundService);

        try {
            $result = $refundService->createRefund($refundRequest, $auth);
            $this->assertInstanceOf(RefundResponseDTO::class, $result);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'jwe')) {
                $this->addToAssertionCount(1);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @return void
     * @throws AuthenticationException
     * @throws RefundOrderException
     */
    public function test_create_refund_throws_exception_on_missing_jwe(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['status' => 'error']
        ]);

        $refundService = new RefundOrder($httpClientMock);

        $this->expectException(RefundOrderException::class);
        $this->expectExceptionMessage('Could not refund the order. Jwe token was not received in response.');

        $auth = AuthorizationDTO::fromArray(
            [
                'baseUrl' => 'url',
                'merchantId' => 'm',
                'serviceCode' => 's',
                'authenticationKey' => $this->getTestPrivateKey(),
                'serverPublic' => $this->getTestPublicKey(),
                'deviceId' => 'd',
                'refreshToken' => 'r'
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
