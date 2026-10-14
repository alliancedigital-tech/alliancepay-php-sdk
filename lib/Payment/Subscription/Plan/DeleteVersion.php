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
 * Class DeleteVersion.
 *
 * Calls the separate `version/delete_v1` endpoint named in the source specification, as opposed to
 * {@see DeletePlanVersion}'s confirmed `plan/delete_v1`. EXPERIMENTAL: neither its exact request/response
 * shape nor its behavioral difference from `plan/delete_v1` is confirmed by a real captured example — the
 * same request/response DTOs are used as a best guess. Verify against the bank's test environment before
 * relying on this endpoint in production.
 */
class DeleteVersion
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
    public function deleteVersion(
        PlanVersionDeleteRequestDTO $deleteRequestDto,
        AuthorizationDTO $authorizationDto
    ): PlanVersionDeleteResponseDTO {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_DELETE_VERSION,
                $deleteRequestDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getDeleteVersionUrl($authorizationDto->getBaseUrl()),
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
                message: 'An unexpected error occurred during version deletion',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
