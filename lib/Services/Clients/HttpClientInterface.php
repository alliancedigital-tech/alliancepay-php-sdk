<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients;

use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;

/**
 * HttpClientInterface.
 */
interface HttpClientInterface
{
    public const METHOD_POST = 'POST';

    /**
     * @param string $method GET, POST, etc.
     * @param string $url Повний URL
     * @param array $options ['headers' => [], 'body' => [], 'query' => []]
     * @return array ['code' => int, 'body' => array, 'headers' => array]
     */
    public function request(string $method, string $url, array $options = []): array;

    /**
     * @param string $requestId
     * @return void
     */
    public function applyRequestId(string $requestId): void;

    /**
     * @param AuthorizationDTO $authorizationDto
     * @param string|array $data
     * @param bool $isJsonContent
     * @return array
     */
    public function buildOptionsFromAuthDto(
        AuthorizationDTO $authorizationDto,
        string|array $data,
        bool $isJsonContent = true
    ): array;

    public function getCreateOrderUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getRefundUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getAuthorizeUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getOperationsUrl(string $baseUrl): string;
}
