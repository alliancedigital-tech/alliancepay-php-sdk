<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Order;

use AlliancePay\Sdk\Exceptions\AuthenticationException;
use AlliancePay\Sdk\Exceptions\CompletionAmountException;
use AlliancePay\Sdk\Exceptions\CreateCompletionException;
use AlliancePay\Sdk\Payment\Dto\Order\OrderCompletionResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestCompletionDTO;
use AlliancePay\Sdk\Payment\Order\CreateOrderCompletion;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class CreateOrderCompletionTest.
 */
class CreateOrderCompletionTest extends TestCase
{
    /**
     * @return AuthorizationDTO
     * @throws AuthenticationException
     */
    private function buildAuthDto(): AuthorizationDTO
    {
        return AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M-123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);
    }

    /**
     * @param int $coinAmount
     * @return OrderRequestCompletionDTO
     */
    private function buildCompletionDTO(int $coinAmount = 1000): OrderRequestCompletionDTO
    {
        return OrderRequestCompletionDTO::fromArray([
            'merchantRequestId' => 'REQ-COMP-001',
            'merchantId' => 'M-123',
            'originalOperationId' => 'ORIG-OP-456',
            'coinAmount' => $coinAmount,
            'date' => '2026-07-30 14:00:00.00+00:00',
            'notificationUrl' => 'https://callback.example.com/callback.php',
        ]);
    }

    /**
     * @return array
     */
    private function fakeCompletionResponseData(): array
    {
        return [
            'type' => 'COMPLETION',
            'rrn' => '621111663467',
            'coinAmount' => 1000,
            'merchantId' => 'M-123',
            'operationId' => '1785409472722Smu-BHsU1og',
            'ecomOperationId' => '9310e5dd-314c-4270-9855-400cef2d9d38',
            'status' => 'SUCCESS',
            'transactionType' => 196,
            'merchantRequestId' => 'REQ-COMP-001',
            'transactionCurrency' => '980',
            'creationDateTime' => '2026.07.30 14:04:32.670',
            'modificationDateTime' => '2026.07.30 14:04:32.670',
            'transactionResponseInfo' => ['actionCode' => '0', 'responseCode' => '00'],
            'productType' => 'PURCHASE',
            'notificationUrl' => 'https://callback.example.com/callback.php',
            'paymentServiceType' => 'CARD',
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'hppOrderId' => '1785409415221z0mFUnqh2mW',
            'processingTerminalId' => 'AE001001',
            'processingMerchantId' => 'AE001001',
            'creatorSystem' => 'HPP',
            'rrnPreauth' => '621111663467',
            'preauthOperationId' => '1785409440842jfUooX2IDOB',
            'preauthCoinAmount' => 1000,
            'preauthEcomOperationId' => '46eeec46-ca4f-455e-8205-d9503c8bd5c1',
        ];
    }

    /**
     * @return void
     * @throws CreateCompletionException
     * @throws AuthenticationException
     */
    public function test_create_completion_returns_order_completion_response_dto(): void
    {
        $auth = $this->buildAuthDto();
        $completionDto = $this->buildCompletionDTO(1000);

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(HttpClientInterface::METHOD_POST, 'https://api.test.ua/completion')
            ->willReturn(['code' => 200, 'body' => $this->fakeCompletionResponseData(), 'headers' => []]);

        $service = new CreateOrderCompletion($httpClientMock);
        $result = $service->createCompletion($completionDto, $auth, 1000);

        $this->assertInstanceOf(OrderCompletionResponseDTO::class, $result);
    }

    /**
     * @return void
     * @throws CreateCompletionException
     * @throws AuthenticationException
     */
    public function test_create_completion_dto_has_correct_type(): void
    {
        $auth = $this->buildAuthDto();
        $completionDto = $this->buildCompletionDTO(1000);

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);
        $httpClientMock->method('request')
            ->willReturn(['code' => 200, 'body' => $this->fakeCompletionResponseData(), 'headers' => []]);

        $service = new CreateOrderCompletion($httpClientMock);
        $result = $service->createCompletion($completionDto, $auth, 1000);

        $this->assertSame('COMPLETION', $result->getType());
        $this->assertSame('SUCCESS', $result->getStatus());
    }

    /**
     * @return void
     * @throws CreateCompletionException
     * @throws AuthenticationException
     */
    public function test_create_completion_uses_post_method(): void
    {
        $auth = $this->buildAuthDto();
        $completionDto = $this->buildCompletionDTO(1000);

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(HttpClientInterface::METHOD_POST, $this->anything())
            ->willReturn(['code' => 200, 'body' => $this->fakeCompletionResponseData(), 'headers' => []]);

        $service = new CreateOrderCompletion($httpClientMock);
        $service->createCompletion($completionDto, $auth, 1000);
    }

    /**
     * @return void
     * @throws CreateCompletionException
     * @throws AuthenticationException
     */
    public function test_create_completion_throws_when_completion_amount_out_of_range(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);
        $httpClientMock->expects($this->never())->method('request');

        $service = new CreateOrderCompletion($httpClientMock);

        $this->expectException(CreateCompletionException::class);
        $service->createCompletion($this->buildCompletionDTO(9999), $this->buildAuthDto(), 1000);
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_create_completion_amount_exception_message_starts_with_amount_validation_failed(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);

        $service = new CreateOrderCompletion($httpClientMock);

        try {
            $service->createCompletion($this->buildCompletionDTO(9999), $this->buildAuthDto(), 1000);
            $this->fail('Expected CreateCompletionException was not thrown.');
        } catch (CreateCompletionException $e) {
            $this->assertStringStartsWith('Amount validation failed:', $e->getMessage());
        }
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_create_completion_amount_exception_wraps_completion_amount_exception(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);

        $service = new CreateOrderCompletion($httpClientMock);

        try {
            $service->createCompletion($this->buildCompletionDTO(9999), $this->buildAuthDto(), 1000);
            $this->fail('Expected CreateCompletionException was not thrown.');
        } catch (CreateCompletionException $e) {
            $this->assertInstanceOf(CompletionAmountException::class, $e->getPrevious());
        }
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_create_completion_amount_exception_payload_forwarded(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);

        $service = new CreateOrderCompletion($httpClientMock);

        try {
            $service->createCompletion($this->buildCompletionDTO(9999), $this->buildAuthDto(), 1000);
            $this->fail('Expected CreateCompletionException was not thrown.');
        } catch (CreateCompletionException $e) {
            $this->assertArrayHasKey('originalCoinAmount', $e->getPayload());
            $this->assertSame(1000, $e->getPayload()['originalCoinAmount']);
        }
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_create_completion_wraps_unexpected_exception(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('getCompletionUrl')->willReturn('https://api.test.ua/completion');
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => 'jws-token']);
        $httpClientMock->method('request')->willThrowException(
            new RuntimeException('unexpected network failure')
        );

        $service = new CreateOrderCompletion($httpClientMock);

        try {
            $service->createCompletion($this->buildCompletionDTO(1000), $this->buildAuthDto(), 1000);
            $this->fail('Expected CreateCompletionException was not thrown.');
        } catch (CreateCompletionException $e) {
            $this->assertStringContainsString('unexpected error', $e->getMessage());
            $this->assertInstanceOf(RuntimeException::class, $e->getPrevious());
        }
    }
}
