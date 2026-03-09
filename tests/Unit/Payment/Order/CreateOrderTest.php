<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Order;

use AlliancePay\Sdk\Exceptions\AuthenticationException;
use AlliancePay\Sdk\Exceptions\CreateOrderException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Order\OrderResponseDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Order\CreateOrder;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;

/**
 * Class CreateOrderTest.
 */
class CreateOrderTest extends TestCase
{
    /**
     * @return void
     * @throws AuthenticationException
     * @throws CreateOrderException
     * @throws ValidateDataException
     */
    public function test_create_order_sends_correct_request_and_returns_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('buildOptionsFromAuthDto')
            ->willReturn(['headers' => ['x-api_version' => 'V1'], 'json' => []]);

        $httpClientMock->method('getCreateOrderUrl')
            ->willReturn('https://api.test.ua/create-order');

        $mockedResponseBody = [
            'body' => [
                'coinAmount' => 1000,
                'ecomOrderId' => 'E-1',
                'statusUrl' => 'https://url.com',
                'merchantId' => 'M-1',
                'hppOrderId' => 'ALB-999-888',
                'redirectUrl' => 'https://redirect.url/pay/123',
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
            ]
        ];

        $httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn([
                'code' => 200,
                'body' => $mockedResponseBody['body'],
                'headers' => []
            ]);

        $service = new CreateOrder($httpClientMock);

        $orderRequest = OrderRequestDTO::fromArray(
            [
                'merchantRequestId' => 'unique_id_123',
                'merchantId' => 'M123',
                'hppPayType' => 'card',
                'coinAmount' => 100,
                'paymentMethods' => ['card'],
                'successUrl' => 'http://ok.com',
                'failUrl' => 'http://fail.com',
                'statusPageType' => 'standard',
                'customerData' => ['senderCustomerId' => 'user_1']
            ]
        );

        $auth = AuthorizationDTO::fromArray(
            [
                'baseUrl' => 'https://api.test.ua',
                'merchantId' => 'M123',
                'serviceCode' => 'S1',
                'authenticationKey' => 'key',
                'deviceId' => 'dev',
                'refreshToken' => 'ref'
            ]
        );

        $result = $service->createOrder($orderRequest, $auth);

        $this->assertInstanceOf(OrderResponseDTO::class, $result);
        $this->assertEquals('ALB-999-888', $result->getHppOrderId());
        $this->assertEquals('https://redirect.url/pay/123', $result->getRedirectUrl());
    }
}
