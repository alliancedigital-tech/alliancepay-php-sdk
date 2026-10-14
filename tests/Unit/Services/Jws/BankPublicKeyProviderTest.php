<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\BankPublicKeyProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class BankPublicKeyProviderTest.
 */
class BankPublicKeyProviderTest extends TestCase
{
    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_fetch_returns_public_key_from_http_client(): void
    {
        $kid = 'kid-' . uniqid();
        $jwk = ['kty' => 'EC', 'crv' => 'P-256', 'x' => 'x-value', 'y' => 'y-value', 'kid' => $kid];

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                HttpClientInterface::METHOD_POST,
                'https://api.test.ua' . HttpClientInterface::PATH_PUBLIC_KEY,
                $this->callback(fn ($options) => $options['json']['kid'] === $kid)
            )
            ->willReturn(['code' => 200, 'body' => $jwk, 'headers' => []]);

        $result = BankPublicKeyProvider::fetch($httpClientMock, 'https://api.test.ua', $kid);

        $this->assertSame($jwk, $result);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_fetch_does_not_cache_and_refetches_on_every_call(): void
    {
        $kid = 'kid-' . uniqid();
        $jwk = ['kty' => 'EC', 'crv' => 'P-256', 'x' => 'x-value', 'y' => 'y-value', 'kid' => $kid];

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->exactly(2))
            ->method('request')
            ->willReturn(['code' => 200, 'body' => $jwk, 'headers' => []]);

        BankPublicKeyProvider::fetch($httpClientMock, 'https://api.test.ua', $kid);
        $second = BankPublicKeyProvider::fetch($httpClientMock, 'https://api.test.ua', $kid);

        $this->assertSame($jwk, $second);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_fetch_throws_when_response_is_missing_required_jwk_fields(): void
    {
        $kid = 'kid-' . uniqid();

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('request')->willReturn(['code' => 200, 'body' => ['kty' => 'EC'], 'headers' => []]);

        $this->expectException(EncryptionException::class);

        BankPublicKeyProvider::fetch($httpClientMock, 'https://api.test.ua', $kid);
    }
}
