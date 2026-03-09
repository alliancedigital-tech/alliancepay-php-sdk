<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Order;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\CreateOrderException;
use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Order\OrderResponseDTO;
use AlliancePay\Sdk\Payment\Order\Validation\ValidateOrderData;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class CreateOrder.
 */
class CreateOrder
{
    use ResponseHandlerTrait;

    public function __construct(
        public ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }
    public function createOrder(OrderRequestDTO $orderDataDto, AuthorizationDTO $authorizationDto): OrderResponseDTO
    {
        try {
            $createOrderRequestId = $orderDataDto->getMerchantRequestId();
            $this->httpClient->applyRequestId($createOrderRequestId);
            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDto,
                $orderDataDto->toArray()
            );

            $createOrderResult = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getCreateOrderUrl($authorizationDto->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($createOrderResult, CreateOrderException::class);

            return OrderResponseDTO::fromArray($createOrderResult['body']);
        } catch (CreateOrderException $exception) {
            throw $exception;
        } catch (ApiException | ValidateDataException $exception) {
            throw new CreateOrderException(
                message: 'Validation error: '. $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception
            );
        } catch (Throwable $e) {
            throw new CreateOrderException(
                message: "An unexpected error occurred during order creation",
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
