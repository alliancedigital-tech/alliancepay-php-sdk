<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Dto\Subscription\Plan;

use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\SearchPlansRequestDTO;
use PHPUnit\Framework\TestCase;

/**
 * Fixture is the real captured request body for `plan/search_v1`.
 */
class SearchPlansRequestDTOTest extends TestCase
{
    /**
     * @return void
     */
    public function test_from_array_and_to_array_round_trip_real_captured_request(): void
    {
        $data = [
            'merchantId' => '137d9304-0368-11ed-b939-0242ac120002',
            'filters' => [
                [
                    'operator' => 'EQUALS',
                    'value' => 'ACTIVE',
                    'field' => 'status',
                ],
            ],
        ];

        $dto = SearchPlansRequestDTO::fromArray($data);

        $this->assertSame('137d9304-0368-11ed-b939-0242ac120002', $dto->getMerchantId());
        $this->assertCount(1, $dto->getFilters());
        $this->assertSame('EQUALS', $dto->getFilters()[0]->getOperator());
        $this->assertSame('status', $dto->getFilters()[0]->getField());
        $this->assertSame('ACTIVE', $dto->getFilters()[0]->getValue());
        $this->assertSame($data, $dto->toArray());
    }
}
