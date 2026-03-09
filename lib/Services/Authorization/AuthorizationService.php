<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Authorization;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Exceptions\AuthenticationException;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Encryption\JweEncryptionService;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use DateTimeImmutable;

/**
 * Class AuthorizationService.
 */
class AuthorizationService
{
    use ResponseHandlerTrait;
    /**
     * @param array $authData
     * @return AuthorizationDTO
     * @throws AuthenticationException
     */
    public function initAuthorization(array $authData)
    {
        $authorizationDto = AuthorizationDto::fromArray($authData);

        if (empty($authorizationDto->getTokenExpirationDateTime())
            || $this->isExpired($authorizationDto->getTokenExpirationDateTime()->getTimestamp())
        ) {
            $httpClient = HttpClientFactory::create();
            $httpClient->applyRequestId(GenerateRequestIdentification::generateRequestId());
            $authorizationDto = $this->authorize($authorizationDto, $httpClient);
        }

        return $authorizationDto;
    }

    /**
     * @param AuthorizationDTO $authorizationDto
     * @param HttpClientInterface $httpClient
     * @return AuthorizationDTO
     * @throws AuthenticationException
     */
    private function authorize(AuthorizationDTO $authorizationDto, HttpClientInterface $httpClient): AuthorizationDTO
    {
        $options = $httpClient->buildOptionsFromAuthDto(
            $authorizationDto,
            ['serviceCode' => $authorizationDto->getServiceCode()]
        );

        try {
            $authResult = $httpClient->request(
                $httpClient::METHOD_POST,
                $httpClient->getAuthorizeUrl($authorizationDto->getBaseUrl()),
                $options
            );
        } catch (ApiException $e) {
            throw new AuthenticationException($e->getMessage());
        }

       $this->handleResponseErrors($authResult, AuthenticationException::class);

        if (!empty($authResult['body']['jwe'])) {
            $decryptedAuthData = JweEncryptionService::decrypt(
                $authorizationDto->getAuthenticationKey(), $authResult['body']['jwe']
            );
        }

        $authData = [
            AuthorizationDTO::AUTH_PROPERTY_BASE_URL => $authorizationDto->getBaseUrl(),
            AuthorizationDTO::AUTH_PROPERTY_MERCHANT_ID => $authorizationDto->getMerchantId(),
            AuthorizationDTO::AUTH_PROPERTY_SERVICE_CODE => $authorizationDto->getServiceCode(),
            AuthorizationDTO::AUTH_PROPERTY_AUTHENTICATION_KEY => $authorizationDto->getAuthenticationKey(),
        ];

        return AuthorizationDTO::fromArray(array_merge($authData, $decryptedAuthData));
    }

    /**
     * @param int $tokenExpirationDateTime
     * @return bool
     */
    private function isExpired(int $tokenExpirationDateTime): bool
    {
        return $tokenExpirationDateTime <= (time() + 30);
    }
}
