<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\UpdatePlanException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\UpdatePlan;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class UpdatePlanTest extends TestCase
{
    /**
     * @return void
     * @throws UpdatePlanException
     * @throws ValidateDataException
     */
    public function test_update_plan_sends_correct_request_and_returns_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with($this->anything(), HttpClientInterface::PATH_UPDATE_PLAN, $this->anything(), HttpClientInterface::CONTENT_TYPE_JWT)
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getUpdatePlanUrl')->willReturn('https://api.test.ua/update-plan');
        $httpClientMock->expects($this->once())->method('request')->willReturn([
            'code' => 200,
            'body' => [
                'planId' => 'plan-1',
                'versionId' => 2,
                'merchantId' => 'M123',
                'name' => 'Updated plan',
                'status' => 'ACTIVE',
                'rules' => [
                    [
                        'paymentNumberFrom' => 1,
                        'paymentNumberTo' => 999,
                        'intervalValue' => 1,
                        'intervalUnit' => 'MONTH',
                        'coinAmount' => 12000,
                    ],
                ],
            ],
            'headers' => [],
        ]);

        $service = new UpdatePlan($httpClientMock);
        $planRequest = PlanRequestDTO::fromArray([
            'planId' => 'plan-1',
            'name' => 'Updated plan',
            'description' => 'Updated plan description',
            'categorySubcategoryIndicator' => 'C101',
            'merchantId' => 'M123',
            'rules' => [
                [
                    'paymentNumberFrom' => 1,
                    'paymentNumberTo' => 999,
                    'intervalValue' => 1,
                    'intervalUnit' => 'MONTH',
                    'coinAmount' => 12000,
                    'currency' => '980',
                ],
            ],
        ]);

        $result = $service->updatePlan($planRequest, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(PlanResponseDTO::class, $result);
        $this->assertSame(2, $result->getVersionId());
    }
}
