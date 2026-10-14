<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\UpdatePlanVersionException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionUpdateRequestDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class UpdatePlanVersion.
 *
 * The "infinite schedule" endpoint — MUST be called immediately after every successful {@see CreatePlan}
 * or {@see UpdatePlan}, or the bank silently applies a hidden expiration to the plan. Prefer
 * {@see PlanLifecycleOrchestrator} unless you have a specific reason to call this endpoint in isolation.
 */
class UpdatePlanVersion
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param PlanVersionUpdateRequestDTO $versionUpdateDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanVersionResponseDTO
     * @throws UpdatePlanVersionException
     */
    public function updatePlanVersion(
        PlanVersionUpdateRequestDTO $versionUpdateDto,
        AuthorizationDTO $authorizationDto
    ): PlanVersionResponseDTO {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_UPDATE_PLAN_VERSION,
                $versionUpdateDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getUpdatePlanVersionUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, UpdatePlanVersionException::class);

            return PlanVersionResponseDTO::fromArray($result['body']);
        } catch (UpdatePlanVersionException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new UpdatePlanVersionException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new UpdatePlanVersionException(
                message: 'An unexpected error occurred during plan version update',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
