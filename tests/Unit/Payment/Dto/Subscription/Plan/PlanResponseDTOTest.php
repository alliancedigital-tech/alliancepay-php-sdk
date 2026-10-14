<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Dto\Subscription\Plan;

use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanResponseDTO;
use PHPUnit\Framework\TestCase;

/**
 * Fixture is the real captured response for `plan/create_v1` (see responses.json in the repo root).
 */
class PlanResponseDTOTest extends TestCase
{
    /**
     * @return void
     */
    public function test_from_array_parses_real_captured_create_plan_response(): void
    {
        $data = [
            'planId' => '410d6d4c-10f2-4900-abdc-ae5cf1c7ee43',
            'versionId' => 1,
            'merchantId' => '137d9304-0368-11ed-b939-0242ac120002',
            'name' => 'description',
            'description' => 'тестовоа',
            'status' => 'ACTIVE',
            'rules' => [
                [
                    'paymentNumberFrom' => 1,
                    'paymentNumberTo' => 2,
                    'intervalValue' => 1,
                    'intervalUnit' => 'DAY',
                    'coinAmount' => 100,
                    'currency' => '980',
                ],
            ],
            'appliesDateTimeFrom' => '2026-09-30 15:47:56.68+03:00',
            'createdAt' => '2026-09-30 15:47:56.68+03:00',
            'updatedAt' => '2026-09-30 15:47:56.68+03:00',
        ];

        $dto = PlanResponseDTO::fromArray($data);

        $this->assertSame('410d6d4c-10f2-4900-abdc-ae5cf1c7ee43', $dto->getPlanId());
        $this->assertIsInt($dto->getVersionId());
        $this->assertSame(1, $dto->getVersionId());
        $this->assertSame('ACTIVE', $dto->getStatus());
        $this->assertCount(1, $dto->getRules());
        $this->assertSame('980', $dto->getRules()[0]->getCurrency());
    }

    /**
     * @return void
     */
    public function test_to_array_round_trips_versionId_as_int(): void
    {
        $dto = PlanResponseDTO::fromArray([
            'planId' => 'p1',
            'versionId' => '2',
            'merchantId' => 'm1',
            'name' => 'n',
            'status' => 'ACTIVE',
            'rules' => [],
        ]);

        $this->assertSame(2, $dto->toArray()['versionId']);
    }
}
