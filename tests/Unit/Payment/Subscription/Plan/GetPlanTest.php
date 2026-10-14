<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\GetPlanException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\GetPlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanDetailsDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\GetPlan;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class GetPlanTest extends TestCase
{
    /**
     * @return void
     * @throws GetPlanException
     */
    public function test_get_plan_sends_only_plan_id_and_returns_details(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_GET_PLAN,
                ['planId' => '4381e925-7d9b-4c71-9a24-2d46fbdeb41e'],
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getGetPlanUrl')->willReturn('https://api.test.ua/plan/get');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => [
                'merchantId' => 'M123',
                'plan' => [
                    'planId' => '4381e925-7d9b-4c71-9a24-2d46fbdeb41e',
                    'name' => 'description',
                    'status' => 'ACTIVE',
                ],
                'versions' => [],
                'subscriptionStatistics' => ['pending' => 0, 'success' => 0, 'fail' => 0, 'canceled' => 0],
            ],
            'headers' => [],
        ]);

        $service = new GetPlan($httpClientMock);
        $dto = new GetPlanRequestDTO('4381e925-7d9b-4c71-9a24-2d46fbdeb41e');

        $result = $service->getPlan($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(PlanDetailsDTO::class, $result);
        $this->assertSame('4381e925-7d9b-4c71-9a24-2d46fbdeb41e', $result->getPlan()->getPlanId());
    }
}
