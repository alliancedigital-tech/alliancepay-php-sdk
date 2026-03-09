<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients\HttpBase;

use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;

/**
 * Class HttpBase.
 */
class HttpBase
{
    private const REQUEST_CONTENT_TYPE_TEXT = 'text/plain';
    private const REQUEST_CONTENT_TYPE_JSON = 'application/json';
    private const X_API_VERSION = 'V1';
    private const ENDPOINT_CREATE_ORDER = '/ecom/execute_request/hpp/v1/create-order';
    private const ENDPOINT_OPERATIONS = '/ecom/execute_request/hpp/v1/operations';
    private const ENDPOINT_REFUND = '/ecom/execute_request/payments/v3/refund';
    private const ENDPOINT_AUTHORIZE = '/api-gateway/authorize_virtual_device';

    /**
     * @param AuthorizationDTO $authorizationDto
     * @param string|array $data
     * @param bool $isJsonContent
     * @return array[]
     */
    public function buildOptionsFromAuthDto(
        AuthorizationDTO $authorizationDto,
        string|array $data,
        bool $isJsonContent = true
    ): array {
        $options = [
            'headers' => [
                'x-api_version' => self::X_API_VERSION,
                'x-device_id' => $authorizationDto->getDeviceId(),
                'x-refresh_token' => $authorizationDto->getRefreshToken(),
                'x-request_id' => $this->requestId ?? uniqid(),
                'Content-Type' => $isJsonContent ? self::REQUEST_CONTENT_TYPE_JSON : self::REQUEST_CONTENT_TYPE_TEXT,
            ]
        ];

        if (!$isJsonContent) {
            $options['body'] = $data;
        }

        if ($data && $isJsonContent) {
            $options['json'] = $data;
            $options['headers']['Accept'] = self::REQUEST_CONTENT_TYPE_JSON;
        }

        return $options;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCreateOrderUrl(string $baseUrl): string
    {
        return $baseUrl . self::ENDPOINT_CREATE_ORDER;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getRefundUrl(string $baseUrl): string
    {
        return $baseUrl . self::ENDPOINT_REFUND;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getAuthorizeUrl(string $baseUrl): string
    {
        return $baseUrl . self::ENDPOINT_AUTHORIZE;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getOperationsUrl(string $baseUrl): string
    {
        return $baseUrl . self::ENDPOINT_OPERATIONS;
    }
}
