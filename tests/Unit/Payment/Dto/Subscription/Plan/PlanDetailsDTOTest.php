<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Dto\Subscription\Plan;

use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanDetailsDTO;
use PHPUnit\Framework\TestCase;

/**
 * Fixture is the real captured response for `plan/get_v1` (see responses.json in the repo root), which
 * returns `versions` and each version's `rules` as JSON objects keyed by a string index, not plain arrays —
 * decoded here via json_decode(..., true) to reproduce exactly what PHP hands to PlanDetailsDTO::fromArray().
 */
class PlanDetailsDTOTest extends TestCase
{
    /**
     * @return void
     */
    public function test_from_array_parses_real_captured_get_plan_response(): void
    {
        $json = <<<'JSON'
{
"merchantId": "137d9304-0368-11ed-b939-0242ac120002",
"plan": {
"planId": "4381e925-7d9b-4c71-9a24-2d46fbdeb41e",
"name": "description",
"description": "тестовоа",
"status": "ACTIVE",
"categorySubcategoryIndicator": "C101",
"createdAt": "2026-09-24 16:35:42.06+03:00",
"updatedAt": "2026-09-24 16:35:42.06+03:00"
},
"versions": {
"1": {
"versionId": 1,
"appliesDateTimeFrom": "2026-09-24 16:35:42.06+03:00",
"rules": {
"1": {
"paymentNumberFrom": 1,
"paymentNumberTo": 2,
"intervalValue": 2,
"intervalUnit": "WEEK",
"coinAmount": 1300,
"currency": "980"
},
"2": {
"paymentNumberFrom": 3,
"paymentNumberTo": 5,
"intervalValue": 22,
"intervalUnit": "DAY",
"coinAmount": 3000,
"currency": "980"
}
}
}
},
"subscriptionStatistics": {
"pending": 1,
"success": 0,
"fail": 0,
"canceled": 0
}
}
JSON;

        $dto = PlanDetailsDTO::fromArray(json_decode($json, true));

        $this->assertSame('4381e925-7d9b-4c71-9a24-2d46fbdeb41e', $dto->getPlan()->getPlanId());
        $this->assertCount(1, $dto->getVersions());
        $this->assertSame(1, $dto->getVersions()[0]->getVersionId());
        $this->assertCount(2, $dto->getVersions()[0]->getRules());
        $this->assertSame(1300, $dto->getVersions()[0]->getRules()[0]->getCoinAmount());
        $this->assertSame(1, $dto->getSubscriptionStatistics()->getPending());
        $this->assertSame(0, $dto->getSubscriptionStatistics()->getSuccess());
    }
}
