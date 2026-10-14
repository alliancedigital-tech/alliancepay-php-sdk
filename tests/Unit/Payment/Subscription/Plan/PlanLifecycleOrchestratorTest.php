<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\CreatePlanException;
use AlliancePay\Sdk\Exceptions\UpdatePlanVersionException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionResponseDTO;
use AlliancePay\Sdk\Payment\Subscription\Plan\CreatePlan;
use AlliancePay\Sdk\Payment\Subscription\Plan\PlanLifecycleOrchestrator;
use AlliancePay\Sdk\Payment\Subscription\Plan\UpdatePlan;
use AlliancePay\Sdk\Payment\Subscription\Plan\UpdatePlanVersion;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use PHPUnit\Framework\TestCase;

/**
 * Covers the plan's single highest-value test: the mandatory "infinite schedule" call must never be
 * silently skipped, and its failure must never be reported as a successful plan creation.
 */
class PlanLifecycleOrchestratorTest extends TestCase
{
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
     * @return PlanRequestDTO
     */
    private function getPlanRequestDto(): PlanRequestDTO
    {
        return PlanRequestDTO::fromArray([
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
        ]);
    }

    /**
     * @return PlanResponseDTO
     */
    private function getPlanResponseDto(): PlanResponseDTO
    {
        return PlanResponseDTO::fromArray([
            'planId' => 'plan-1',
            'versionId' => 1,
            'merchantId' => 'M123',
            'name' => 'Monthly subscription',
            'status' => 'ACTIVE',
            'rules' => [],
        ]);
    }

    /**
     * @return void
     * @throws CreatePlanException
     */
    public function test_create_with_infinite_schedule_is_fully_successful_when_both_calls_succeed(): void
    {
        $createPlanMock = $this->createMock(CreatePlan::class);
        $createPlanMock->expects($this->once())
            ->method('createPlan')
            ->willReturn($this->getPlanResponseDto());

        $updatePlanVersionMock = $this->createMock(UpdatePlanVersion::class);
        $updatePlanVersionMock->expects($this->once())
            ->method('updatePlanVersion')
            ->willReturn(new PlanVersionResponseDTO('plan-1', 1, [], null, null, null));

        $orchestrator = new PlanLifecycleOrchestrator($createPlanMock, null, $updatePlanVersionMock);

        $result = $orchestrator->createPlanWithInfiniteSchedule($this->getPlanRequestDto(), $this->getAuthDto());

        $this->assertTrue($result->isFullySuccessful());
        $this->assertTrue($result->isInfiniteScheduleApplied());
        $this->assertNull($result->getInfiniteScheduleError());
    }

    /**
     * The core requirement: if plan creation succeeds but the infinite-schedule call fails, the orchestrator
     * must NOT throw and must NOT report full success — it must return a result that makes the partial
     * failure explicit.
     *
     * @return void
     * @throws CreatePlanException
     */
    public function test_create_with_infinite_schedule_reports_partial_failure_without_throwing(): void
    {
        $createPlanMock = $this->createMock(CreatePlan::class);
        $createPlanMock->method('createPlan')->willReturn($this->getPlanResponseDto());

        $updatePlanVersionMock = $this->createMock(UpdatePlanVersion::class);
        $updatePlanVersionMock->method('updatePlanVersion')
            ->willThrowException(new UpdatePlanVersionException('provider timeout'));

        $orchestrator = new PlanLifecycleOrchestrator($createPlanMock, null, $updatePlanVersionMock);

        $result = $orchestrator->createPlanWithInfiniteSchedule($this->getPlanRequestDto(), $this->getAuthDto());

        $this->assertFalse($result->isFullySuccessful());
        $this->assertFalse($result->isInfiniteScheduleApplied());
        $this->assertSame('provider timeout', $result->getInfiniteScheduleError());
        $this->assertSame('plan-1', $result->getPlanResponse()->getPlanId());
    }

    /**
     * @return void
     */
    public function test_create_with_infinite_schedule_propagates_create_plan_failure_without_attempting_version_call(): void
    {
        $createPlanMock = $this->createMock(CreatePlan::class);
        $createPlanMock->method('createPlan')->willThrowException(new CreatePlanException('bad request'));

        $updatePlanVersionMock = $this->createMock(UpdatePlanVersion::class);
        $updatePlanVersionMock->expects($this->never())->method('updatePlanVersion');

        $orchestrator = new PlanLifecycleOrchestrator($createPlanMock, null, $updatePlanVersionMock);

        $this->expectException(CreatePlanException::class);
        $this->expectExceptionMessage('bad request');

        $orchestrator->createPlanWithInfiniteSchedule($this->getPlanRequestDto(), $this->getAuthDto());
    }

    /**
     * @return void
     */
    public function test_update_with_infinite_schedule_uses_update_plan(): void
    {
        $updatePlanMock = $this->createMock(UpdatePlan::class);
        $updatePlanMock->expects($this->once())
            ->method('updatePlan')
            ->willReturn($this->getPlanResponseDto());

        $updatePlanVersionMock = $this->createMock(UpdatePlanVersion::class);
        $updatePlanVersionMock->expects($this->once())
            ->method('updatePlanVersion')
            ->willReturn(new PlanVersionResponseDTO('plan-1', 1, [], null, null, null));

        $orchestrator = new PlanLifecycleOrchestrator(null, $updatePlanMock, $updatePlanVersionMock);

        $result = $orchestrator->updatePlanWithInfiniteSchedule($this->getPlanRequestDto(), $this->getAuthDto());

        $this->assertTrue($result->isFullySuccessful());
    }
}
