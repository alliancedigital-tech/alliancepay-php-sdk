<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Authorization\Dto;

use AlliancePay\Sdk\Exceptions\AuthenticationException;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;

/**
 * Class AuthorizationDto.
 */
final class AuthorizationDTO
{
    public const AUTH_PROPERTY_BASE_URL = 'baseUrl';
    public const AUTH_PROPERTY_MERCHANT_ID = 'merchantId';
    public const AUTH_PROPERTY_SERVICE_CODE = 'serviceCode';
    public const AUTH_PROPERTY_AUTHENTICATION_KEY = 'authenticationKey';
    public const AUTH_PROPERTY_REFRESH_TOKEN = 'refreshToken';
    public const AUTH_PROPERTY_AUTH_TOKEN = 'authToken';
    public const AUTH_PROPERTY_DEVICE_ID = 'deviceId';
    public const AUTH_PROPERTY_SERVER_PUBLIC = 'serverPublic';
    public const AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME = 'tokenExpirationDateTime';

    private function __construct(
        private string $baseUrl,
        private string $merchantId,
        private string $serviceCode,
        private string $authenticationKey,
        private string $refreshToken = '',
        private string $authToken = '',
        private string $deviceId = '',
        private string $serverPublic = '',
        private ?DateTimeImmutable $tokenExpirationDateTime = null,
    ) {
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getServiceCode(): string
    {
        return $this->serviceCode;
    }

    /**
     * @return string
     */
    public function getAuthenticationKey(): string
    {
        return $this->authenticationKey;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * @return string
     */
    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    /**
     * @return string
     */
    public function getServerPublic(): string
    {
        return $this->serverPublic;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getTokenExpirationDateTime(): ?DateTimeImmutable
    {
        return $this->tokenExpirationDateTime;
    }

    /**
     * @param array $authData
     * @return self
     * @throws AuthenticationException
     */
    public static function fromArray(array $authData): self
    {
        if (empty($authData)
            || empty($authData[self::AUTH_PROPERTY_BASE_URL])
            || empty($authData[self::AUTH_PROPERTY_MERCHANT_ID])
            || empty($authData[self::AUTH_PROPERTY_SERVICE_CODE])
            || empty($authData[self::AUTH_PROPERTY_AUTHENTICATION_KEY])
        ) {
            throw new AuthenticationException(
                'Base URL, Merchant ID, Service Code and Authentication Key are required.'
            );
        }

        if (!empty($authData[self::AUTH_PROPERTY_SERVER_PUBLIC])
            && is_array($authData[self::AUTH_PROPERTY_SERVER_PUBLIC])
        ) {
            $authData[self::AUTH_PROPERTY_SERVER_PUBLIC] = json_encode(
                $authData[self::AUTH_PROPERTY_SERVER_PUBLIC]
            );
        }

        if (!empty($authData[self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME])) {
            $authData[self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME]
                = DateTimeImmutableProvider::fromString(
                    $authData[self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME],
                    DateTimeImmutableProvider::KYIV_TIMEZONE
            );
        } else {
            $authData[self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME]
                = DateTimeImmutableProvider::fromString(
                    'now',
                    DateTimeImmutableProvider::KYIV_TIMEZONE
            );
        }

        return new self(
            baseUrl: $authData[self::AUTH_PROPERTY_BASE_URL],
            merchantId: $authData[self::AUTH_PROPERTY_MERCHANT_ID],
            serviceCode: $authData[self::AUTH_PROPERTY_SERVICE_CODE],
            authenticationKey: $authData[self::AUTH_PROPERTY_AUTHENTICATION_KEY],
            refreshToken: $authData[self::AUTH_PROPERTY_REFRESH_TOKEN] ?? '',
            authToken: $authData[self::AUTH_PROPERTY_AUTH_TOKEN] ?? '',
            deviceId: $authData[self::AUTH_PROPERTY_DEVICE_ID] ?? '',
            serverPublic: $authData[self::AUTH_PROPERTY_SERVER_PUBLIC] ?? '',
            tokenExpirationDateTime: $authData[self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME]
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::AUTH_PROPERTY_BASE_URL => $this->baseUrl,
            self::AUTH_PROPERTY_MERCHANT_ID => $this->merchantId,
            self::AUTH_PROPERTY_SERVICE_CODE => $this->serviceCode,
            self::AUTH_PROPERTY_AUTHENTICATION_KEY => $this->authenticationKey,
            self::AUTH_PROPERTY_REFRESH_TOKEN => $this->refreshToken,
            self::AUTH_PROPERTY_AUTH_TOKEN => $this->authToken,
            self::AUTH_PROPERTY_DEVICE_ID => $this->deviceId,
            self::AUTH_PROPERTY_SERVER_PUBLIC => $this->serverPublic,
            self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME =>
                $this->tokenExpirationDateTime->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
        ];
    }
}
