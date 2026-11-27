<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Fiscalization;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationResponseDTO;
use AlliancePay\Sdk\Payment\Fiscalization\Callback\FiscalizationCallbackHandler;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\CallbackPayloadResolver;
use PHPUnit\Framework\TestCase;

/**
 * Class FiscalizationCallbackHandlerTest.
 */
class FiscalizationCallbackHandlerTest extends TestCase
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

    private function handler(?CallbackPayloadResolver $resolver = null): FiscalizationCallbackHandler
    {
        return new FiscalizationCallbackHandler($this->createMock(HttpClientInterface::class), $resolver);
    }

    public function test_handle_maps_payload(): void
    {
        $dto = $this->handler()->handle($this->auth(), [
            'operationId' => 'OP-1',
            'merchantRequestId' => 'REQ-1',
            'status' => 'SUCCESS',
            'fiscalNumber' => 'FN-1',
            'receiptUrl' => 'https://r/1',
            'modificationDateTime' => '2026-09-03T10:40:00.000+03:00',
        ]);

        $fiscalization = $dto->getFiscalization();
        $this->assertTrue($fiscalization->isSuccess());
        $this->assertTrue($fiscalization->isFinal());
        $this->assertSame('FN-1', $fiscalization->getFiscalNumber());
        $this->assertSame('OP-1', $fiscalization->getOperationId());
    }

    public function test_parser_tolerates_missing_fields(): void
    {
        $dto = $this->handler()->handle($this->auth(), ['operationId' => 'OP-1']);

        $this->assertNull($dto->getFiscalization()->getFiscalNumber());
        $this->assertNull($dto->getFiscalization()->getModificationDateTime());
        $this->assertSame([], $dto->getFiscalization()->getProducts());
    }

    public function test_payload_without_keys_is_rejected(): void
    {
        $this->expectException(CallbackException::class);

        $this->handler()->handle($this->auth(), ['status' => 'SUCCESS']);
    }

    public function test_invalid_signature_propagates_callback_exception(): void
    {
        $resolver = $this->createMock(CallbackPayloadResolver::class);
        $resolver->method('resolve')->willThrowException(new CallbackException('JWS callback verification failed'));

        $this->expectException(CallbackException::class);
        $this->expectExceptionMessage('verification failed');

        $this->handler($resolver)->handle($this->auth());
    }

    public function test_is_newer_than_guards_against_stale_callbacks(): void
    {
        $new = FiscalizationResponseDTO::fromArray([
            'operationId' => 'OP-1', 'status' => 'PENDING',
            'modificationDateTime' => '2026-09-03T10:40:00.000+03:00',
        ]);
        $old = FiscalizationResponseDTO::fromArray([
            'operationId' => 'OP-1', 'status' => 'PENDING',
            'modificationDateTime' => '2026-09-03T10:39:00.000+03:00',
        ]);

        $this->assertTrue($new->isNewerThan($old->getModificationDateTime()));
        $this->assertFalse($old->isNewerThan($new->getModificationDateTime()));
        $this->assertTrue($old->isNewerThan(null));
    }

    public function test_dates_are_parsed_with_their_zone(): void
    {
        $dto = FiscalizationResponseDTO::fromArray([
            'operationId' => 'OP-1',
            'fiscalizationDateTime' => '2026-09-03T10:39:09.540+03:00',
        ]);

        $this->assertSame('2026-09-03T07:39:09+00:00', $dto->getFiscalizationDateTime()
            ->setTimezone(new \DateTimeZone('UTC'))->format(DATE_ATOM));
    }
}
