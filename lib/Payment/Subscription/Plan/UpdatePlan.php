<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\UpdatePlanException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class UpdatePlan.
 *
 * Like {@see CreatePlan}, updating a plan's rules does NOT keep its "infinite schedule" in effect on its
 * own — prefer {@see PlanLifecycleOrchestrator::updatePlanWithInfiniteSchedule()} unless you have a specific
 * reason to call this endpoint in isolation.
 */
class UpdatePlan
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param PlanRequestDTO $planRequestDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanResponseDTO
     * @throws UpdatePlanException
     */
    public function updatePlan(PlanRequestDTO $planRequestDto, AuthorizationDTO $authorizationDto): PlanResponseDTO
    {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_UPDATE_PLAN,
                $planRequestDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getUpdatePlanUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, UpdatePlanException::class);

            return PlanResponseDTO::fromArray($result['body']);
        } catch (UpdatePlanException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new UpdatePlanException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new UpdatePlanException(
                message: 'An unexpected error occurred during plan update',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
