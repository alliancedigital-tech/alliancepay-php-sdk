<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\DeletePlanVersionException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionDeleteRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanVersionDeleteResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class DeletePlanVersion.
 *
 * Calls `plan/delete_v1`, confirmed against a real captured response (marks the given plan version's
 * status as DELETED). Distinct from {@see DeleteVersion}, which calls the separate `version/delete_v1`
 * endpoint named in the source specification.
 */
class DeletePlanVersion
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param PlanVersionDeleteRequestDTO $deleteRequestDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanVersionDeleteResponseDTO
     * @throws DeletePlanVersionException
     */
    public function deletePlanVersion(
        PlanVersionDeleteRequestDTO $deleteRequestDto,
        AuthorizationDTO $authorizationDto
    ): PlanVersionDeleteResponseDTO {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_DELETE_PLAN_VERSION,
                $deleteRequestDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getDeletePlanVersionUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, DeletePlanVersionException::class);

            return PlanVersionDeleteResponseDTO::fromArray($result['body']);
        } catch (DeletePlanVersionException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new DeletePlanVersionException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new DeletePlanVersionException(
                message: 'An unexpected error occurred during plan version deletion',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
