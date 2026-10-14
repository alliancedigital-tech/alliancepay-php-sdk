<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\DeletePlanVersionException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionDeleteRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionDeleteResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\DeletePlanVersion;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

/**
 * Fixture response mirrors the real captured response for `plan/delete_v1`.
 */
class DeletePlanVersionTest extends TestCase
{
    /**
     * @return void
     * @throws DeletePlanVersionException
     */
    public function test_delete_plan_version_calls_confirmed_plan_delete_endpoint(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_DELETE_PLAN_VERSION,
                $this->anything(),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getDeletePlanVersionUrl')->willReturn('https://api.test.ua/plan/delete');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['planId' => '410d6d4c-10f2-4900-abdc-ae5cf1c7ee43', 'versionId' => 2, 'versionStatus' => 'DELETED'],
            'headers' => [],
        ]);

        $service = new DeletePlanVersion($httpClientMock);
        $dto = new PlanVersionDeleteRequestDTO('410d6d4c-10f2-4900-abdc-ae5cf1c7ee43', 2);

        $result = $service->deletePlanVersion($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(PlanVersionDeleteResponseDTO::class, $result);
        $this->assertSame('DELETED', $result->getVersionStatus());
        $this->assertSame(2, $result->getVersionId());
    }
}
