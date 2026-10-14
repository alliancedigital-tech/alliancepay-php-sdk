<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\GetPlanException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\GetPlanRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanDetailsDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class GetPlan.
 *
 * Fetches full plan details (versions, rules, subscription statistics) fresh from the bank — the same
 * "always fetch fresh, never trust a local cache" principle the source specification recommends for
 * support diagnostic pages (section 4.9).
 */
class GetPlan
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param GetPlanRequestDTO $getPlanDto
     * @param AuthorizationDTO $authorizationDto
     * @return PlanDetailsDTO
     * @throws GetPlanException
     */
    public function getPlan(GetPlanRequestDTO $getPlanDto, AuthorizationDTO $authorizationDto): PlanDetailsDTO
    {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_GET_PLAN,
                $getPlanDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getGetPlanUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, GetPlanException::class);

            return PlanDetailsDTO::fromArray($result['body']);
        } catch (GetPlanException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new GetPlanException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new GetPlanException(
                message: 'An unexpected error occurred while fetching the plan',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
