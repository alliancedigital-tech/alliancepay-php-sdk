<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Fiscalization;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Exceptions\FiscalizationException;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationResponseDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use Throwable;

/**
 * Class CreateFiscalization.
 */
class CreateFiscalization
{
    use ResponseHandlerTrait;

    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClientFactory::create();
    }

    /**
     * @param FiscalizationRequestDTO $requestDTO
     * @param AuthorizationDTO $authorizationDTO
     * @return FiscalizationResponseDTO
     * @throws FiscalizationException
     */
    public function createFiscalization(
        FiscalizationRequestDTO $requestDTO,
        AuthorizationDTO $authorizationDTO
    ): FiscalizationResponseDTO {
        try {
            $requestId = $requestDTO->getMerchantRequestId();
            $this->httpClient->applyRequestId($requestId);

            $options = $this->httpClient->buildOptionsFromAuthDto(
                $authorizationDTO,
                HttpClientInterface::PATH_FISCALIZATION_CREATE,
                $requestDTO->toArray()
            );

            $response = $this->httpClient->request(
                $this->httpClient::METHOD_POST,
                $this->httpClient->getFiscalizationCreateUrl($authorizationDTO->getBaseUrl()),
                $options
            );

            $this->handleResponseErrors($response, FiscalizationException::class);
            $this->handleHttpErrors($response);

            return FiscalizationResponseDTO::fromArray($response['body']);
        } catch (FiscalizationException $e) {
            throw $e;
        } catch (ApiException | EncryptionException $e) {
            throw new FiscalizationException(
                message: 'Fiscalization request failed: ' . $e->getMessage(),
                payload: ['request_id' => $requestId ?? null],
                code: (int)$e->getCode(),
                previous: $e
            );
        } catch (Throwable $e) {
            throw new FiscalizationException(
                message: 'An unexpected error occurred during fiscalization',
                payload: [],
                code: 0,
                previous: $e
            );
        }
    }

    /**
     * @param array $response
     * @return void
     * @throws FiscalizationException
     */
    private function handleHttpErrors(array $response): void
    {
        $code = (int)($response['code'] ?? 200);
        $body = is_array($response['body'] ?? null) ? $response['body'] : [];

        if ($code < 400 && !empty($body)) {
            return;
        }

        $msgCode = $body['msgCode'] ?? null;
        $message = sprintf(
            'Fiscalization request rejected (HTTP %d%s)%s',
            $code,
            $msgCode ? ', ' . $msgCode : '',
            isset($body['msgAttrs']) ? ': ' . json_encode($body['msgAttrs'], JSON_UNESCAPED_UNICODE) : ''
        );

        throw new FiscalizationException($message, $body, $code);
    }
}
