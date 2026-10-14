<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Subscription\Plan;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\SearchPlansException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\SearchPlansRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Subscription\Plan\SearchPlansResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class SearchPlans.
 *
 * Lists/filters a merchant's plans — the list endpoint the source specification assumed did not exist for
 * this provider (its "reconciliation job" section was written without it). Consuming applications can use
 * this to reconcile locally cached plan state against the bank, instead of relying solely on webhooks.
 */
class SearchPlans
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param SearchPlansRequestDTO $searchPlansDto
     * @param AuthorizationDTO $authorizationDto
     * @return SearchPlansResponseDTO
     * @throws SearchPlansException
     */
    public function searchPlans(
        SearchPlansRequestDTO $searchPlansDto,
        AuthorizationDTO $authorizationDto
    ): SearchPlansResponseDTO {
        try {
            $this->httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                HttpClientInterface::PATH_SEARCH_PLANS,
                $searchPlansDto->toArray(),
                HttpClientInterface::CONTENT_TYPE_JWT
            );

            $result = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getSearchPlansUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($result, SearchPlansException::class);

            return SearchPlansResponseDTO::fromArray($result['body']);
        } catch (SearchPlansException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new SearchPlansException(
                message: 'Validation error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new SearchPlansException(
                message: 'An unexpected error occurred while searching plans',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
