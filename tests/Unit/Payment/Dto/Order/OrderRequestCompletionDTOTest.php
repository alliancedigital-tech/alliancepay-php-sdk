<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Order;

use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestCompletionDTO;
use PHPUnit\Framework\TestCase;

/**
 * Class OrderRequestCompletionDTOTest.
 */
class OrderRequestCompletionDTOTest extends TestCase
{
    /**
     * @return array
     */
    private function validData(): array
    {
        return [
            'merchantRequestId' => '6c8ea695-9dbd-484d-8b6b-2033f3a1bb23',
            'merchantId' => 'ab6c9936-2946-4f12-9202-712930bccd19',
            'originalOperationId' => '1734612061099XJ-BEtZIEwA',
            'coinAmount' => 25000,
            'date' => '2024-12-19 17:59:23.23+00:00',
            'notificationUrl' => 'https://example.com/callback',
        ];
    }

    /**
     * @return void
     */
    public function test_from_array_hydrates_all_fields(): void
    {
        $dto = OrderRequestCompletionDTO::fromArray($this->validData());

        $this->assertSame('6c8ea695-9dbd-484d-8b6b-2033f3a1bb23', $dto->getMerchantRequestId());
        $this->assertSame('ab6c9936-2946-4f12-9202-712930bccd19', $dto->getMerchantId());
        $this->assertSame('1734612061099XJ-BEtZIEwA', $dto->getOriginalOperationId());
        $this->assertSame(25000, $dto->getCoinAmount());
        $this->assertSame('2024-12-19 17:59:23.23+00:00', $dto->toArray()['date']);
        $this->assertSame('https://example.com/callback', $dto->getNotificationUrl());
    }

    /**
     * @return void
     */
    public function test_from_array_casts_coin_amount_to_int(): void
    {
        $data = $this->validData();
        $data['coinAmount'] = '12345';

        $dto = OrderRequestCompletionDTO::fromArray($data);

        $this->assertIsInt($dto->getCoinAmount());
        $this->assertSame(12345, $dto->getCoinAmount());
    }

    /**
     * @return void
     */
    public function test_to_array_returns_all_six_keys(): void
    {
        $dto = OrderRequestCompletionDTO::fromArray($this->validData());
        $result = $dto->toArray();

        $this->assertArrayHasKey('merchantRequestId', $result);
        $this->assertArrayHasKey('merchantId', $result);
        $this->assertArrayHasKey('originalOperationId', $result);
        $this->assertArrayHasKey('coinAmount', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('notificationUrl', $result);
        $this->assertCount(6, $result);
    }

    /**
     * @return void
     */
    public function test_to_array_preserves_values(): void
    {
        $data = $this->validData();
        $dto = OrderRequestCompletionDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertSame($data['merchantRequestId'], $result['merchantRequestId']);
        $this->assertSame($data['merchantId'], $result['merchantId']);
        $this->assertSame($data['originalOperationId'], $result['originalOperationId']);
        $this->assertSame($data['coinAmount'], $result['coinAmount']);
        $this->assertSame($data['date'], $result['date']);
        $this->assertSame($data['notificationUrl'], $result['notificationUrl']);
    }

    /**
     * @return void
     */
    public function test_round_trip_from_array_to_array(): void
    {
        $data = $this->validData();
        $result = OrderRequestCompletionDTO::fromArray($data)->toArray();

        $this->assertSame($data['merchantRequestId'], $result['merchantRequestId']);
        $this->assertSame($data['merchantId'], $result['merchantId']);
        $this->assertSame($data['originalOperationId'], $result['originalOperationId']);
        $this->assertSame((int)$data['coinAmount'], $result['coinAmount']);
        $this->assertSame($data['date'], $result['date']);
        $this->assertSame($data['notificationUrl'], $result['notificationUrl']);
    }

    /**
     * @return void
     */
    public function test_coin_amount_is_int_in_to_array(): void
    {
        $dto = OrderRequestCompletionDTO::fromArray($this->validData());
        $result = $dto->toArray();

        $this->assertIsInt($result['coinAmount']);
    }
}
