<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\CreatePlanException;
use AlliancePay\Sdk\Exceptions\UpdatePlanException;
use AlliancePay\Sdk\Exceptions\UpdatePlanVersionException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanCreationResultDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionUpdateRequestDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;

/**
 * Class PlanLifecycleOrchestrator.
 *
 * Convenience wrapper around {@see CreatePlan}/{@see UpdatePlan} + {@see UpdatePlanVersion}. The bank
 * requires the "infinite schedule" call immediately after every plan create/update, or it silently applies
 * a hidden expiration — this orchestrator makes that two-step sequence hard to forget, without hiding the
 * partial-failure state: if plan creation itself fails, the original exception propagates unchanged and
 * the infinite-schedule call is never attempted; if only the infinite-schedule call fails, this does NOT
 * throw — it returns a {@see PlanCreationResultDTO} whose `isFullySuccessful()` is false, so a caller can
 * never mistake "plan created" for "plan created and safe from silent expiration".
 *
 * `createPlan()`/`updatePlan()` are never retried here: `merchantRequestId`-style retries are not safe for
 * these calls in this API (a retry creates a new object rather than returning the original — see the
 * SDK README's warning on this), so retrying a failed creation is left entirely to the caller.
 */
class PlanLifecycleOrchestrator
{
    public function __construct(
        private ?CreatePlan $createPlan = null,
        private ?UpdatePlan $updatePlan = null,
        private ?UpdatePlanVersion $updatePlanVersion = null,
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->createPlan = $createPlan ?? new CreatePlan($this->httpClient);
        $this->updatePlan = $updatePlan ?? new UpdatePlan($this->httpClient);
        $this->updatePlanVersion = $updatePlanVersion ?? new UpdatePlanVersion($this->httpClient);
    }

    /**
     * @param PlanRequestDTO $planRequestDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanCreationResultDTO
     * @throws CreatePlanException
     */
    public function createPlanWithInfiniteSchedule(
        PlanRequestDTO $planRequestDto,
        AuthorizationDTO $authorizationDto
    ): PlanCreationResultDTO {
        $planResponse = $this->createPlan->createPlan($planRequestDto, $authorizationDto);

        return $this->applyInfiniteSchedule($planResponse, $authorizationDto);
    }

    /**
     * @param PlanRequestDTO $planRequestDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanCreationResultDTO
     * @throws UpdatePlanException
     */
    public function updatePlanWithInfiniteSchedule(
        PlanRequestDTO $planRequestDto,
        AuthorizationDTO $authorizationDto
    ): PlanCreationResultDTO {
        $planResponse = $this->updatePlan->updatePlan($planRequestDto, $authorizationDto);

        return $this->applyInfiniteSchedule($planResponse, $authorizationDto);
    }

    /**
     * @param PlanResponseDTO $planResponse
     * @param AuthorizationDTO $authorizationDto
     * @return PlanCreationResultDTO
     */
    private function applyInfiniteSchedule(
        PlanResponseDTO $planResponse,
        AuthorizationDTO $authorizationDto
    ): PlanCreationResultDTO {
        try {
            $this->updatePlanVersion->updatePlanVersion(
                new PlanVersionUpdateRequestDTO(
                    planId: $planResponse->getPlanId(),
                    versionId: $planResponse->getVersionId(),
                    infiniteVersion: true,
                ),
                $authorizationDto
            );

            return new PlanCreationResultDTO($planResponse, infiniteScheduleApplied: true);
        } catch (UpdatePlanVersionException $exception) {
            return new PlanCreationResultDTO(
                $planResponse,
                infiniteScheduleApplied: false,
                infiniteScheduleError: $exception->getMessage()
            );
        }
    }
}
