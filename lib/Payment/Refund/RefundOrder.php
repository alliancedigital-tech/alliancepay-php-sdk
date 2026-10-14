<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Refund;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Exceptions\RefundOrderException;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundResponseDTO;
use AlliancePay\Sdk\Payment\Refund\Validation\ValidateRefundData;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class RefundOrder.
 */
class RefundOrder
{
    use ResponseHandlerTrait;

    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param RefundRequestDTO $refundRequestDTO
     * @param AuthorizationDTO $authorizationDTO
     * @return RefundResponseDTO
     * @throws RefundOrderException Головне виключення, яке містить всі деталі
     */
    public function createRefund(
        RefundRequestDTO $refundRequestDTO,
        AuthorizationDTO $authorizationDTO
    ): RefundResponseDTO {
        try {
            $refundRequestId = $refundRequestDTO->getMerchantRequestId();
            $this->httpClient->applyRequestId($refundRequestId);

            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDTO,
                HttpClientInterface::PATH_REFUND,
                $refundRequestDTO->toArray()
            );

            $response = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getRefundUrl($authorizationDTO->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($response, RefundOrderException::class);

            return RefundResponseDTO::createFromArray($response['body']);

        } catch (RefundOrderException $e) {
            throw $e;
        } catch (ApiException | EncryptionException $e) {
            throw new RefundOrderException(
                message: "Refund process failed: " . $e->getMessage(),
                payload: ['request_id' => $refundRequestId ?? null],
                code: (int) $e->getCode(),
                previous: $e
            );
        } catch (Throwable $e) {
            throw new RefundOrderException(
                message: "An unexpected error occurred during refund",
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
