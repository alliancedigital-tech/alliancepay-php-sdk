<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\SearchPlansException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\SearchPlansRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\SearchPlansResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\SearchPlans;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class SearchPlansTest extends TestCase
{
    /**
     * @return void
     * @throws SearchPlansException
     */
    public function test_search_plans_sends_filters_and_returns_response_dto(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('buildOptionsFromAuthDto')
            ->with(
                $this->anything(),
                HttpClientInterface::PATH_SEARCH_PLANS,
                $this->anything(),
                HttpClientInterface::CONTENT_TYPE_JWT
            )
            ->willReturn(['headers' => [], 'body' => '']);
        $httpClientMock->method('getSearchPlansUrl')->willReturn('https://api.test.ua/plan/search');
        $httpClientMock->method('request')->willReturn([
            'code' => 200,
            'body' => [
                'currentPage' => 0,
                'plans' => [
                    ['planId' => 'plan-1', 'name' => 'Plan 1', 'status' => 'ACTIVE'],
                    ['planId' => 'plan-2', 'name' => 'Plan 2', 'status' => 'ACTIVE'],
                ],
            ],
            'headers' => [],
        ]);

        $service = new SearchPlans($httpClientMock);
        $dto = SearchPlansRequestDTO::fromArray([
            'merchantId' => 'M123',
            'filters' => [['operator' => 'EQUALS', 'value' => 'ACTIVE', 'field' => 'status']],
        ]);

        $result = $service->searchPlans($dto, AuthorizationDTO::fromArray([
            'baseUrl' => 'https://api.test.ua',
            'merchantId' => 'M123',
            'jwsPrivateKey' => 'test-private-key-pem',
            'jwsKeyId' => 'test-kid',
        ]));

        $this->assertInstanceOf(SearchPlansResponseDTO::class, $result);
        $this->assertCount(2, $result->getPlans());
    }
}
