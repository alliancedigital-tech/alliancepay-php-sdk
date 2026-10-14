<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Callback\CallbackHandler;
use AlliancePay\Sdk\Payment\Dto\Callback\CallbackDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for the {@see \AlliancePay\Sdk\Services\Jws\CallbackPayloadResolver} extraction —
 * confirms CallbackHandler::handle() with an explicit payload still builds the same CallbackDTO it did
 * before the raw-body/signature-verification logic was pulled out into a shared resolver.
 */
class CallbackHandlerTest extends TestCase
{
    /**
     * @return void
     * @throws CallbackException
     */
    public function test_handle_with_explicit_payload_builds_callback_dto(): void
    {
        $authDto = AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);

        $payload = [
            'ecomOrderId' => 'ECOM-1',
            'coinAmount' => 1000,
            'merchantId' => 'M123',
            'statusUrl' => 'https://site.com/status',
            'redirectUrl' => 'https://site.com/redirect',
            'notificationUrl' => 'https://site.com/callback',
            'notificationEncryption' => false,
            'hppOrderId' => 'HPP-1',
            'hppPayType' => 'PURCHASE',
            'hppDirectType' => 'CARD',
            'merchantRequestId' => 'REQ-1',
            'createDate' => '2026.02.13 10:06:01.927',
            'paymentMethods' => ['CARD'],
            'orderStatus' => 'SUCCESS',
            'expiredOrderDate' => '2026.02.14 10:06:01.927',
            'operation' => [
                'type' => OperationPurchaseDTO::OPERATION_TYPE,
                'creationDateTime' => '2026.01.23 11:11:59.903',
                'modificationDateTime' => '2026.01.23 11:12:00.174',
                'transactionResponseInfo' => [],
                'notificationEncryption' => false,
                'notificationSignature' => false,
            ],
        ];

        $handler = new CallbackHandler();
        $dto = $handler->handle($authDto, $payload);

        $this->assertInstanceOf(CallbackDTO::class, $dto);
        $this->assertSame('HPP-1', $dto->toArray()['hppOrderId']);
    }
}
