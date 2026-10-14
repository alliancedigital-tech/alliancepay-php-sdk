<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\CreatePlanException;
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
 * Class CreatePlan.
 *
 * Creating a plan does NOT make it usable on its own — the bank silently applies a hidden expiration unless
 * a separate "infinite schedule" call ({@see UpdatePlanVersion}) is made right after. Prefer
 * {@see PlanLifecycleOrchestrator::createPlanWithInfiniteSchedule()} unless you have a specific reason to
 * call this endpoint in isolation.
 */
class CreatePlan
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
     * @throws CreatePlanException
     */
    public function createPlan(PlanRequestDTO $planRequestDto, AuthorizationDTO $authorizationDto): PlanResponseDTO
    {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_CREATE_PLAN,
                $planRequestDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getCreatePlanUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, CreatePlanException::class);

            return PlanResponseDTO::fromArray($result['body']);
        } catch (CreatePlanException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new CreatePlanException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new CreatePlanException(
                message: 'An unexpected error occurred during plan creation',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
