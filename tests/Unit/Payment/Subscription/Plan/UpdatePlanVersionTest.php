<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\UpdatePlanVersionException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionUpdateRequestDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\UpdatePlanVersion;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class UpdatePlanVersionTest extends TestCase
{
    /**
     * @return void
     * @throws UpdatePlanVersionException
     */
    public function test_update_plan_version_defaults_infinite_version_to_true(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_UPDATE_PLAN_VERSION,
                $this->callback(fn (array $data) => $data['infiniteVersion'] === true),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getUpdatePlanVersionUrl')->willReturn('https://api.test.ua/version');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['planId' => 'plan-1', 'versionId' => 1, 'rules' => []],
            'headers' => [],
        ]);

        $service = new UpdatePlanVersion($httpClientMock);
        $dto = new PlanVersionUpdateRequestDTO('plan-1', 1);

        $result = $service->updatePlanVersion($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(PlanVersionResponseDTO::class, $result);
    }
}
