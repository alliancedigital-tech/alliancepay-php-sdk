<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\CreatePlanException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\CreatePlan;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class CreatePlanTest extends TestCase
{
    /**
     * @return array
     */
    private function getPlanData(): array
    {
        return [
            'name' => 'Monthly subscription',
            'description' => 'Monthly subscription plan',
            'categorySubcategoryIndicator' => 'C101',
            'merchantId' => 'M123',
            'rules' => [
                [
                    'paymentNumberFrom' => 1,
                    'paymentNumberTo' => 999,
                    'intervalValue' => 1,
                    'intervalUnit' => 'MONTH',
                    'coinAmount' => 10000,
                    'currency' => '980',
                ],
            ],
        ];
    }

    /**
     * @return AuthorizationDTO
     */
    private function getAuthDto(): AuthorizationDTO
    {
        return AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]);
    }

    /**
     * @return void
     * @throws CreatePlanException
     * @throws ValidateDataException
     */
    public function test_create_plan_sends_jwt_content_type_and_returns_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_CREATE_PLAN,
                $this->anything(),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);

        $httpClientMock->method('getCreatePlanUrl')->willReturn('https://api.test.ua/create-plan');

        $httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn([
                'code' => 200,
                'body' => [
                    'planId' => 'plan-1',
                    'versionId' => 1,
                    'merchantId' => 'M123',
                    'name' => 'Monthly subscription',
                    'status' => 'ACTIVE',
                    'rules' => $this->getPlanData()['rules'],
                ],
                'headers' => [],
            ]);

        $service = new CreatePlan($httpClientMock);
        $planRequest = PlanRequestDTO::fromArray($this->getPlanData());

        $result = $service->createPlan($planRequest, $this->getAuthDto());

        $this->assertInstanceOf(PlanResponseDTO::class, $result);
        $this->assertSame('plan-1', $result->getPlanId());
        $this->assertSame(1, $result->getVersionId());
    }

    /**
     * @return void
     * @throws CreatePlanException
     * @throws ValidateDataException
     */
    public function test_create_plan_wraps_provider_error_response(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getCreatePlanUrl')->willReturn('https://api.test.ua/create-plan');
        $httpClientMock->method('request')->willReturn([
            'code' => 400,
            'body' => ['msgType' => 'ERROR', 'msgCode' => 'E1', 'msgText' => 'Invalid plan'],
            'headers' => [],
        ]);

        $service = new CreatePlan($httpClientMock);
        $planRequest = PlanRequestDTO::fromArray($this->getPlanData());

        $this->expectException(CreatePlanException::class);

        $service->createPlan($planRequest, $this->getAuthDto());
    }
}
