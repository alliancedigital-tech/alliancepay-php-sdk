<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Order;

use AlliancePay\Sdk\Payment\Dto\Order\OrderCompletionResponseDTO;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Class OrderCompletionResponseDTOTest.
 */
class OrderCompletionResponseDTOTest extends TestCase
{
    private function fullPayload(): array
    {
        return [
            'type' => 'COMPLETION',
            'rrn' => '111111111111',
            'coinAmount' => 100,
            'merchantId' => '137d9304-0368-11ed-b939-0242ac120002',
            'operationId' => '9999409415221z0mFUnddddd',
            'ecomOperationId' => '9310e5dd-314c-4270-9855-400cef2d9d38',
            'status' => 'SUCCESS',
            'transactionType' => 196,
            'merchantRequestId' => '4e61c85e-a8eb-4740-a26a-43689de7bc9b',
            'transactionCurrency' => '980',
            'creationDateTime' => '2026.07.30 14:04:32.670',
            'modificationDateTime' => '2026.07.30 14:04:32.670',
            'transactionResponseInfo' => [
                'actionCode' => '0',
                'responseCode' => '00',
                'description' => 'Операція успішна',
            ],
            'productType' => 'PURCHASE',
            'notificationUrl' => 'https://callback.example.com/callback.php',
            'paymentServiceType' => 'CARD',
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'hppOrderId' => '9999409415221z0mFUnddddd',
            'processingTerminalId' => 'AE001001',
            'processingMerchantId' => 'AE001001',
            'creatorSystem' => 'HPP',
            'rrnPreauth' => '111111111111',
            'preauthOperationId' => '9999409415221z0mFUnddddd',
            'preauthCoinAmount' => 100,
            'preauthEcomOperationId' => '46eeec46-ca4f-455e-8205-d9503c8bd5c1',
        ];
    }

    public function test_from_array_hydrates_type(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('COMPLETION', $dto->getType());
    }

    public function test_from_array_hydrates_rrn(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('111111111111', $dto->getRrn());
    }

    public function test_from_array_hydrates_coin_amount_as_int(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertIsInt($dto->getCoinAmount());
        $this->assertSame(100, $dto->getCoinAmount());
    }

    public function test_from_array_hydrates_merchant_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('137d9304-0368-11ed-b939-0242ac120002', $dto->getMerchantId());
    }

    public function test_from_array_hydrates_operation_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('9999409415221z0mFUnddddd', $dto->getOperationId());
    }

    public function test_from_array_hydrates_ecom_operation_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('9310e5dd-314c-4270-9855-400cef2d9d38', $dto->getEcomOperationId());
    }

    public function test_from_array_hydrates_status(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('SUCCESS', $dto->getStatus());
    }

    public function test_from_array_hydrates_transaction_type_as_int(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertIsInt($dto->getTransactionType());
        $this->assertSame(196, $dto->getTransactionType());
    }

    public function test_from_array_hydrates_merchant_request_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('4e61c85e-a8eb-4740-a26a-43689de7bc9b', $dto->getMerchantRequestId());
    }

    public function test_from_array_hydrates_transaction_currency(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('980', $dto->getTransactionCurrency());
    }

    public function test_from_array_parses_creation_date_time_as_date_time_immutable(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getCreationDateTime());
        $this->assertSame('2026-07-30', $dto->getCreationDateTime()->format('Y-m-d'));
    }

    public function test_from_array_parses_modification_date_time_as_date_time_immutable(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getModificationDateTime());
        $this->assertSame('2026-07-30', $dto->getModificationDateTime()->format('Y-m-d'));
    }

    public function test_from_array_hydrates_transaction_response_info(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $info = $dto->getTransactionResponseInfo();
        $this->assertIsArray($info);
        $this->assertSame('0', $info['actionCode']);
        $this->assertSame('00', $info['responseCode']);
        $this->assertSame('Операція успішна', $info['description']);
    }

    public function test_from_array_hydrates_product_type(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('PURCHASE', $dto->getProductType());
    }

    public function test_from_array_hydrates_notification_url(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('https://callback.example.com/callback.php', $dto->getNotificationUrl());
    }

    public function test_from_array_hydrates_payment_service_type(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('CARD', $dto->getPaymentServiceType());
    }

    public function test_from_array_hydrates_notification_encryption_as_false(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertFalse($dto->isNotificationEncryption());
    }

    public function test_from_array_hydrates_notification_signature_as_false(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertFalse($dto->isNotificationSignature());
    }

    public function test_from_array_hydrates_hpp_order_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('9999409415221z0mFUnddddd', $dto->getHppOrderId());
    }

    public function test_from_array_hydrates_processing_terminal_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('AE001001', $dto->getProcessingTerminalId());
    }

    public function test_from_array_hydrates_processing_merchant_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('AE001001', $dto->getProcessingMerchantId());
    }

    public function test_from_array_hydrates_creator_system(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('HPP', $dto->getCreatorSystem());
    }

    public function test_from_array_hydrates_rrn_preauth(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('111111111111', $dto->getRrnPreauth());
    }

    public function test_from_array_hydrates_preauth_operation_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('9999409415221z0mFUnddddd', $dto->getPreauthOperationId());
    }

    public function test_from_array_hydrates_preauth_coin_amount_as_int(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertIsInt($dto->getPreauthCoinAmount());
        $this->assertSame(100, $dto->getPreauthCoinAmount());
    }

    public function test_from_array_hydrates_preauth_ecom_operation_id(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());

        $this->assertSame('46eeec46-ca4f-455e-8205-d9503c8bd5c1', $dto->getPreauthEcomOperationId());
    }

    public function test_from_array_with_minimal_payload_sets_nullable_fields_to_null(): void
    {
        $minimal = [
            'type' => 'COMPLETION',
            'rrn' => '111',
            'coinAmount' => 500,
            'merchantId' => 'M-1',
            'operationId' => 'OP-1',
            'ecomOperationId' => 'ECOM-1',
            'status' => 'SUCCESS',
            'transactionCurrency' => '980',
            'hppOrderId' => 'HPP-1',
        ];

        $dto = OrderCompletionResponseDTO::fromArray($minimal);

        $this->assertNull($dto->getTransactionType());
        $this->assertNull($dto->getMerchantRequestId());
        $this->assertNull($dto->getCreationDateTime());
        $this->assertNull($dto->getModificationDateTime());
        $this->assertNull($dto->getProductType());
        $this->assertNull($dto->getNotificationUrl());
        $this->assertNull($dto->getPaymentServiceType());
        $this->assertNull($dto->getProcessingTerminalId());
        $this->assertNull($dto->getProcessingMerchantId());
        $this->assertNull($dto->getCreatorSystem());
        $this->assertNull($dto->getRrnPreauth());
        $this->assertNull($dto->getPreauthOperationId());
        $this->assertNull($dto->getPreauthCoinAmount());
        $this->assertNull($dto->getPreauthEcomOperationId());
    }

    public function test_from_array_with_minimal_payload_transaction_response_info_defaults_to_empty_array(): void
    {
        $minimal = [
            'type' => 'COMPLETION',
            'rrn' => '',
            'coinAmount' => 0,
            'merchantId' => '',
            'operationId' => '',
            'ecomOperationId' => '',
            'status' => '',
            'transactionCurrency' => '',
            'hppOrderId' => '',
        ];

        $dto = OrderCompletionResponseDTO::fromArray($minimal);

        $this->assertSame([], $dto->getTransactionResponseInfo());
    }

    public function test_from_array_notification_encryption_defaults_to_false(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray([
            'type' => 'COMPLETION',
            'rrn' => '',
            'coinAmount' => 0,
            'merchantId' => '',
            'operationId' => '',
            'ecomOperationId' => '',
            'status' => '',
            'transactionCurrency' => '',
            'hppOrderId' => '',
        ]);

        $this->assertFalse($dto->isNotificationEncryption());
        $this->assertFalse($dto->isNotificationSignature());
    }

    public function test_to_array_contains_all_expected_keys(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());
        $result = $dto->toArray();

        $expectedKeys = [
            'type', 'rrn', 'coinAmount', 'merchantId', 'operationId', 'ecomOperationId',
            'status', 'transactionType', 'merchantRequestId', 'transactionCurrency',
            'creationDateTime', 'modificationDateTime', 'transactionResponseInfo',
            'productType', 'notificationUrl', 'paymentServiceType',
            'notificationEncryption', 'notificationSignature', 'hppOrderId',
            'processingTerminalId', 'processingMerchantId', 'creatorSystem',
            'rrnPreauth', 'preauthOperationId', 'preauthCoinAmount', 'preauthEcomOperationId',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Key '{$key}' missing from toArray()");
        }
    }

    public function test_to_array_serializes_dates_as_string(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());
        $result = $dto->toArray();

        $this->assertIsString($result['creationDateTime']);
        $this->assertIsString($result['modificationDateTime']);
        $this->assertStringStartsWith('2026-07-30', $result['creationDateTime']);
    }

    public function test_to_array_null_dates_are_null(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray([
            'type' => 'COMPLETION',
            'rrn' => '',
            'coinAmount' => 0,
            'merchantId' => '',
            'operationId' => '',
            'ecomOperationId' => '',
            'status' => '',
            'transactionCurrency' => '',
            'hppOrderId' => '',
        ]);
        $result = $dto->toArray();

        $this->assertNull($result['creationDateTime']);
        $this->assertNull($result['modificationDateTime']);
    }

    public function test_to_array_preserves_scalar_values(): void
    {
        $dto = OrderCompletionResponseDTO::fromArray($this->fullPayload());
        $result = $dto->toArray();

        $this->assertSame('COMPLETION', $result['type']);
        $this->assertSame('111111111111', $result['rrn']);
        $this->assertSame(100, $result['coinAmount']);
        $this->assertSame('SUCCESS', $result['status']);
        $this->assertSame(196, $result['transactionType']);
        $this->assertFalse($result['notificationEncryption']);
        $this->assertFalse($result['notificationSignature']);
        $this->assertSame('111111111111', $result['rrnPreauth']);
        $this->assertSame('9999409415221z0mFUnddddd', $result['preauthOperationId']);
        $this->assertSame(100, $result['preauthCoinAmount']);
    }
}
