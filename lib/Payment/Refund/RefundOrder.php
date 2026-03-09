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
use AlliancePay\Sdk\Services\Encryption\JweEncryptionService;
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
            $publicKey = json_decode($authorizationDTO->getServerPublic(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RefundOrderException("Invalid server public key format (JSON error)");
            }

            $preparedRefundData = JweEncryptionService::encrypt(
                $refundRequestDTO->toArray(),
                $publicKey
            );

            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDTO,
                $preparedRefundData,
                false
            );

            $response = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getRefundUrl($authorizationDTO->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($response, RefundOrderException::class);

            if (empty($response['body']['jwe'])) {
                throw new RefundOrderException(
                    'Could not refund the order. Jwe token was not received in response.'
                );
            }

            $decryptedRefundData = JweEncryptionService::decrypt(
                $authorizationDTO->getAuthenticationKey(),
                $response['body']['jwe']
            );

            return RefundResponseDTO::createFromArray($decryptedRefundData);

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
