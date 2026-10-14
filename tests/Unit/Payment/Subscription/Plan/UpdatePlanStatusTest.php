<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\UpdatePlanException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanStatusUpdateRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanStatusUpdateResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\UpdatePlanStatus;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class UpdatePlanStatusTest extends TestCase
{
    /**
     * @return void
     * @throws UpdatePlanException
     */
    public function test_update_plan_status_sends_correct_request_and_returns_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getUpdatePlanStatusUrl')->willReturn('https://api.test.ua/status');
        $httpClientMock->expects($this->once())->method('request')->willReturn([
            'code' => 200,
            'body' => ['planId' => 'plan-1', 'merchantId' => 'M123', 'status' => 'PAUSED'],
            'headers' => [],
        ]);

        $service = new UpdatePlanStatus($httpClientMock);
        $dto = new PlanStatusUpdateRequestDTO('plan-1', 'M123', 'PAUSED');

        $result = $service->updatePlanStatus($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(PlanStatusUpdateResponseDTO::class, $result);
        $this->assertSame('PAUSED', $result->getStatus());
    }

    /**
     * @return void
     */
    public function test_update_plan_status_wraps_provider_error_as_update_plan_exception(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('buildOptionsFromAuthDto')->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getUpdatePlanStatusUrl')->willReturn('https://api.test.ua/status');
        $httpClientMock->method('request')->willReturn([
            'code' => 400,
            'body' => ['msgType' => 'ERROR', 'msgCode' => 'E1', 'msgText' => 'Plan not found'],
            'headers' => [],
        ]);

        $service = new UpdatePlanStatus($httpClientMock);
        $dto = new PlanStatusUpdateRequestDTO('plan-unknown', 'M123', 'PAUSED');

        $this->expectException(UpdatePlanException::class);

        $service->updatePlanStatus($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));
    }
}
