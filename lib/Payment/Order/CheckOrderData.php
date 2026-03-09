<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Order;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\CheckOrderDataException;
use AlliancePay\Sdk\Payment\Dto\Order\OrderDataResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class CheckOrderData.
 */
class CheckOrderData
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param string $hppOrderId
     * @param AuthorizationDTO $authorizationDto
     * @return OrderDataResponseDTO
     * @throws CheckOrderDataException
     */
    public function checkOrderData(string $hppOrderId, AuthorizationDTO $authorizationDto): OrderDataResponseDTO
    {
        try {
            if (empty($hppOrderId)) {
                throw new CheckOrderDataException('HPP Order ID cannot be empty');
            }
            $createOrderRequestId = GenerateRequestIdentification::generateRequestId();
            $this->httpClient->applyRequestId($createOrderRequestId);
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                ['hppOrderId' => $hppOrderId]
            );

            $orderDataResult = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getOperationsUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($orderDataResult);

            return OrderDataResponseDTO::fromArray($orderDataResult['body']);
        } catch (CheckOrderDataException $exception) {
            throw $exception;
        } catch (ApiException $exception) {
            throw new CheckOrderDataException(
                message: 'Check order data API error: ' . $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new CheckOrderDataException(
                message: "An unexpected error occurred during refund",
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
