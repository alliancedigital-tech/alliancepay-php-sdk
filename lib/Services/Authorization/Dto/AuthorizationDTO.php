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
    public const AUTH_PROPERTY_AUTHENTICATION_KEY = 'authenticationKey';
    public const AUTH_PROPERTY_AUTH_TOKEN = 'authToken';
    public const AUTH_PROPERTY_SERVER_PUBLIC = 'serverPublic';
    public const AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME = 'tokenExpirationDateTime';
    public const AUTH_PROPERTY_JWS_PRIVATE_KEY = 'jwsPrivateKey';
    public const AUTH_PROPERTY_JWS_KEY_ID = 'jwsKeyId';

    private function __construct(
        private string $baseUrl,
        private string $merchantId,
        private string $jwsPrivateKey,
        private string $jwsKeyId,
        private string $authenticationKey = '',
        private string $authToken = '',
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
    public function getAuthenticationKey(): string
    {
        return $this->authenticationKey;
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
    public function getServerPublic(): string
    {
        return $this->serverPublic;
    }

    /**
     * @return string
     */
    public function getJwsPrivateKey(): string
    {
        return $this->jwsPrivateKey;
    }

    /**
     * @return string
     */
    public function getJwsKeyId(): string
    {
        return $this->jwsKeyId;
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
            || empty($authData[self::AUTH_PROPERTY_JWS_PRIVATE_KEY])
            || empty($authData[self::AUTH_PROPERTY_JWS_KEY_ID])
        ) {
            throw new AuthenticationException(
                'Base URL, Merchant ID, JWS Private Key and JWS Key ID are required.'
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
            jwsPrivateKey: $authData[self::AUTH_PROPERTY_JWS_PRIVATE_KEY],
            jwsKeyId: $authData[self::AUTH_PROPERTY_JWS_KEY_ID],
            authenticationKey: $authData[self::AUTH_PROPERTY_AUTHENTICATION_KEY] ?? '',
            authToken: $authData[self::AUTH_PROPERTY_AUTH_TOKEN] ?? '',
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
            self::AUTH_PROPERTY_JWS_PRIVATE_KEY => $this->jwsPrivateKey,
            self::AUTH_PROPERTY_JWS_KEY_ID => $this->jwsKeyId,
            self::AUTH_PROPERTY_AUTHENTICATION_KEY => $this->authenticationKey,
            self::AUTH_PROPERTY_AUTH_TOKEN => $this->authToken,
            self::AUTH_PROPERTY_SERVER_PUBLIC => $this->serverPublic,
            self::AUTH_PROPERTY_TOKEN_EXPIRATION_DATE_TIME =>
                $this->tokenExpirationDateTime->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
        ];
    }
}
