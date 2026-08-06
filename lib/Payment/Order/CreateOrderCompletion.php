<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Order;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\CompletionAmountException;
use AlliancePay\Sdk\Exceptions\CreateCompletionException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Payment\Dto\Order\OrderCompletionResponseDTO;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestCompletionDTO;
use AlliancePay\Sdk\Payment\Order\Validation\ValidateOrderData;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Encryption\JweEncryptionService;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class CreateOrderCompletion.
 */
class CreateOrderCompletion
{
    use ResponseHandlerTrait;

    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param OrderRequestCompletionDTO $completionRequestDTO
     * @param AuthorizationDTO $authorizationDTO
     * @param int $originalCoinAmount Original pre-authorised amount for ±20% validation.
     * @return OrderCompletionResponseDTO
     * @throws CreateCompletionException
     */
    public function createCompletion(
        OrderRequestCompletionDTO $completionRequestDTO,
        AuthorizationDTO $authorizationDTO,
        int $originalCoinAmount,
    ): OrderCompletionResponseDTO
    {
        try {
            $completionRequestId = $completionRequestDTO->getMerchantRequestId();
            $this->httpClient->applyRequestId($completionRequestId);

            ValidateOrderData::validateCompletionAmount(
                $originalCoinAmount,
                $completionRequestDTO->getCoinAmount()
            );

            $publicKey = json_decode($authorizationDTO->getServerPublic(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new CreateCompletionException('Invalid server public key format (JSON error)');
            }

            $encryptedPayload = JweEncryptionService::encrypt(
                $completionRequestDTO->toArray(),
                $publicKey
            );

            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDTO,
                $encryptedPayload,
                false
            );

            $response = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getCompletionUrl($authorizationDTO->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($response, CreateCompletionException::class);

            if (empty($response['body']['jwe'])) {
                throw new CreateCompletionException(
                    'Could not complete the order. JWE token was not received in response.'
                );
            }

            $decryptedData = JweEncryptionService::decrypt(
                $authorizationDTO->getAuthenticationKey(),
                $response['body']['jwe']
            );

            return OrderCompletionResponseDTO::fromArray($decryptedData);

        } catch (CreateCompletionException $e) {
            throw $e;
        } catch (CompletionAmountException $e) {
            throw new CreateCompletionException(
                message: 'Amount validation failed: ' . $e->getMessage(),
                payload: $e->getPayload(),
                code: $e->getCode(),
                previous: $e
            );
        } catch (ApiException|EncryptionException $e) {
            throw new CreateCompletionException(
                message: 'Completion process failed: ' . $e->getMessage(),
                payload: ['request_id' => $completionRequestId ?? null],
                code: (int)$e->getCode(),
                previous: $e
            );
        } catch (Throwable $e) {
            throw new CreateCompletionException(
                message: 'An unexpected error occurred during completion',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }
}
