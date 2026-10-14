<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\DeletePlanVersionException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionDeleteRequestDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\DeleteVersion;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

/**
 * EXPERIMENTAL endpoint (see {@see DeleteVersion} docblock) — this test only confirms the SDK calls the
 * distinct `version/delete_v1` path, not that the shape matches a confirmed real bank response.
 */
class DeleteVersionTest extends TestCase
{
    /**
     * @return void
     * @throws DeletePlanVersionException
     */
    public function test_delete_version_calls_the_distinct_version_delete_endpoint(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_DELETE_VERSION,
                $this->anything(),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getDeleteVersionUrl')->willReturn('https://api.test.ua/version/delete');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => ['planId' => 'plan-1', 'versionId' => 3, 'versionStatus' => 'DELETED'],
            'headers' => [],
        ]);

        $service = new DeleteVersion($httpClientMock);
        $dto = new PlanVersionDeleteRequestDTO('plan-1', 3);

        $result = $service->deleteVersion($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertSame('DELETED', $result->getVersionStatus());
    }
}
