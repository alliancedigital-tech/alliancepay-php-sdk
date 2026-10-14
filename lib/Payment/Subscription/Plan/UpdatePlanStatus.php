<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\UpdatePlanException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanStatusUpdateRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanStatusUpdateResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class UpdatePlanStatus.
 */
class UpdatePlanStatus
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param PlanStatusUpdateRequestDTO $statusUpdateDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanStatusUpdateResponseDTO
     * @throws UpdatePlanException
     */
    public function updatePlanStatus(
        PlanStatusUpdateRequestDTO $statusUpdateDto,
        AuthorizationDTO $authorizationDto
    ): PlanStatusUpdateResponseDTO {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_UPDATE_PLAN_STATUS,
                $statusUpdateDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getUpdatePlanStatusUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, UpdatePlanException::class);

            return PlanStatusUpdateResponseDTO::fromArray($result['body']);
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
                message: 'An unexpected error occurred during plan status update',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
