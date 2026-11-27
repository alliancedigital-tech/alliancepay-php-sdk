<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Fiscalization;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\FiscalizationException;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationProductDTO;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationResponseDTO;
use AlliancePay\Sdk\Payment\Fiscalization\CreateFiscalization;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateFiscalizationTest.
 */
class CreateFiscalizationTest extends TestCase
{
    private function auth(): AuthorizationDTO
    {
        return AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M1',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);
    }

    private function request(): FiscalizationRequestDTO
    {
        return new FiscalizationRequestDTO(
            merchantRequestId: 'REQ-1',
            merchantId: 'M1',
            operationId: 'OP-1',
            notificationUrl: 'https://merchant.example.com/cb',
            products: [new FiscalizationProductDTO('1', 'Товар', 1.0, '0001', 10.0)]
        );
    }

    /**
     * @return HttpClientInterface&MockObject
     */
    private function client(mixed $response): HttpClientInterface
    {
        $client = $this->createMock(HttpClientInterface::class);
        $client->method('getFiscalizationCreateUrl')->willReturn('https://api.test.ua/fiscalization');
        $client->method('buildOptionsFromAuthDto')->willReturn(['body' => 'jws']);

        if ($response instanceof \Throwable) {
            $client->method('request')->willThrowException($response);
        } else {
            $client->method('request')->willReturn($response);
        }

        return $client;
    }

    private function body(array $overrides = []): array
    {
        return array_merge([
            'merchantRequestId' => 'REQ-1',
            'merchantId' => 'M1',
            'operationId' => 'OP-1',
            'operationType' => 'PURCHASE',
            'status' => 'PENDING',
            'creationDateTime' => '2026-09-03T10:39:09.540+03:00',
            'modificationDateTime' => '2026-09-03T10:39:09.540+03:00',
        ], $overrides);
    }

    public function test_pending_response(): void
    {
        $client = $this->client(['code' => 200, 'body' => $this->body()]);
        $client->expects($this->once())->method('applyRequestId')->with('REQ-1');

        $result = (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());

        $this->assertTrue($result->isPending());
        $this->assertFalse($result->isFinal());
        $this->assertSame('PURCHASE', $result->getOperationType());
        $this->assertSame('+03:00', $result->getCreationDateTime()->format('P'));
    }

    public function test_success_response(): void
    {
        $client = $this->client(['code' => 200, 'body' => $this->body([
            'status' => 'SUCCESS',
            'fiscalNumber' => '123456',
            'receiptUrl' => 'https://receipt.example/1',
            'fiscalizationDateTime' => '2026-09-03T10:40:00.000+03:00',
        ])]);

        $result = (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());

        $this->assertInstanceOf(FiscalizationResponseDTO::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('123456', $result->getFiscalNumber());
        $this->assertSame('https://receipt.example/1', $result->getReceiptUrl());
        $this->assertNotNull($result->getFiscalizationDateTime());
    }

    public function test_fail_response(): void
    {
        $client = $this->client(['code' => 200, 'body' => $this->body([
            'status' => 'FAIL',
            'errorCode' => '541',
            'errorDescription' => 'Помилка під час підписання',
        ])]);

        $result = (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());

        $this->assertTrue($result->isFailed());
        $this->assertSame('541', $result->getErrorCode());
        $this->assertSame('Помилка під час підписання', $result->getErrorDescription());
    }

    /**
     * @dataProvider businessErrors
     */
    public function test_http_400_business_errors(string $msgCode): void
    {
        $client = $this->client([
            'code' => 400,
            'body' => ['msgCode' => $msgCode, 'msgAttrs' => ['products']],
        ]);

        try {
            (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());
            $this->fail('Exception expected');
        } catch (FiscalizationException $e) {
            $this->assertSame($msgCode, $e->getMsgCode());
            $this->assertSame(400, $e->getCode());
            $this->assertStringContainsString($msgCode, $e->getMessage());
        }
    }

    public static function businessErrors(): array
    {
        return [
            [FiscalizationException::MSG_CODE_MISSING_REQUIRED_FIELD],
            [FiscalizationException::MSG_CODE_INVALID_FORMAT],
            [FiscalizationException::MSG_CODE_PAYMENT_NOT_FOUND],
            [FiscalizationException::MSG_CODE_ORIGINAL_RECEIPT_NOT_FOUND],
        ];
    }

    public function test_http_500_is_wrapped(): void
    {
        $client = $this->client(['code' => 500, 'body' => []]);

        $this->expectException(FiscalizationException::class);
        $this->expectExceptionMessage('HTTP 500');

        (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());
    }

    public function test_network_timeout_is_wrapped(): void
    {
        $client = $this->client(new ApiException('cURL Error (28): Operation timed out'));

        $this->expectException(FiscalizationException::class);
        $this->expectExceptionMessage('timed out');

        (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());
    }

    public function test_business_error_in_standard_envelope_is_wrapped(): void
    {
        $client = $this->client(['code' => 200, 'body' => [
            'msgType' => 'ERROR', 'msgCode' => 'E1', 'msgText' => 'Rejected',
        ]]);

        $this->expectException(FiscalizationException::class);

        (new CreateFiscalization($client))->createFiscalization($this->request(), $this->auth());
    }
}
