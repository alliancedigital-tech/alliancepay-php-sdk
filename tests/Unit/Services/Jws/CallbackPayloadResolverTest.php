<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Jws;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\CallbackPayloadResolver;
use PHPUnit\Framework\TestCase;

class CallbackPayloadResolverTest extends TestCase
{
    /**
     * @return void
     * @throws CallbackException
     */
    public function test_it_returns_explicit_payload_without_reading_raw_body(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->never())->method('request');

        $resolver = new CallbackPayloadResolver($httpClientMock);

        $authDto = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);

        $result = $resolver->resolve($authDto, ['hppOrderId' => 'HPP-1']);

        $this->assertSame(['hppOrderId' => 'HPP-1'], $result);
    }
}
